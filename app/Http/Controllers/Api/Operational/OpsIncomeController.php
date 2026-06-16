<?php

namespace App\Http\Controllers\Api\Operational;

use App\Enums\OpsSourceType;
use App\Enums\OpsTransferConfirmationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operational\OpsIncomeStoreRequest;
use App\Http\Requests\Operational\OpsIncomeUpdateRequest;
use App\Http\Resources\Operational\OpsIncomeResource;
use App\Models\OpsEditLog;
use App\Models\OpsIncome;
use App\Models\OpsTransferConfirmation;
use App\Services\Operational\OpsFileService;
use App\Services\Operational\OpsNotificationService;
use App\Services\SubCompanyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpsIncomeController extends Controller
{
    use ScopesOperationalBySubCompany;

    protected array $sortableColumns = ['name', 'date', 'amount', 'source_type'];

    public function __construct(
        protected OpsFileService $fileService,
        protected OpsNotificationService $notificationService,
        protected SubCompanyService $subCompanyService,
    ) {}

    public function index(Request $request)
    {
        $orderByKey = in_array($request->input('order_by_key', 'date'), $this->sortableColumns)
            ? $request->input('order_by_key', 'date')
            : 'date';
        $orderByValue = strtoupper($request->input('order_by_value', 'DESC')) === 'ASC' ? 'DESC' : 'ASC';

        $incomes = OpsIncome::with(['mandor', 'subCompany', 'createdBy', 'transferConfirmation', 'editLogs'])
            ->when(true, fn ($query) => $this->applySubCompanyFilter($query, $request))
            ->when($request->date_from, fn($q, $date) => $q->whereDate('date', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('date', '<=', $date))
            ->when(
                $request->mandor_uuid,
                fn($q, $uuid) =>
                $q->whereHas('mandor', fn($m) => $m->where('uuid', $uuid))
            )
            ->when($request->search, function ($query, $search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('operational.incomes.list'),
            'data' => OpsIncomeResource::collection($incomes),
        ]);
    }

    public function store(OpsIncomeStoreRequest $request)
    {
        $editWindowDays = config('operational.expense_edit_window_days');
        $requestDate = Carbon::parse($request->date)->startOfDay();
        $limitDate = now()->subDays($editWindowDays)->startOfDay();
        if ($requestDate->lt($limitDate)) {
            return response()->json([
                'success' => false,
                'message' => __('operational.incomes.store_window_expired', ['days' => $editWindowDays]),
                'code' => 422,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $companyId = $request->user()->company_id;
            $mandor = $this->subCompanyService->resolveMandor($request->mandor_uuid, $companyId);
            $subCompany = $this->subCompanyService->resolveForAdmin(
                $request->sub_company_uuid,
                $companyId,
                $mandor->id
            );

            $income = OpsIncome::create([
                'name' => $request->name,
                'amount' => $request->amount,
                'date' => $request->date,
                'proof_file' => $this->fileService->storeProof($request->file('proof_file')),
                'note' => $request->note,
                'source_type' => OpsSourceType::MANDOR,
                'mandor_id' => $mandor->id,
                'sub_company_id' => $subCompany->id,
                'created_by' => $request->user()->id,
                'company_id' => $companyId,
            ]);

            $confirmation = OpsTransferConfirmation::create([
                'confirmable_type' => $income->getMorphClass(),
                'confirmable_id' => $income->id,
                'status' => OpsTransferConfirmationStatus::PENDING,
                'company_id' => $companyId,
            ]);

            $this->notificationService->notifyMandorIncomePending($mandor, $income, $confirmation);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('operational.incomes.stored'),
                'data' => new OpsIncomeResource(
                    $income->load(['mandor', 'subCompany', 'createdBy', 'transferConfirmation'])
                ),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(OpsIncome $opsIncome)
    {
        return response()->json([
            'success' => true,
            'message' => __('operational.incomes.detail'),
            'data' => new OpsIncomeResource(
                $opsIncome->load(['mandor', 'subCompany', 'createdBy', 'transferConfirmation', 'editLogs'])
            ),
        ]);
    }

    public function update(OpsIncomeUpdateRequest $request, OpsIncome $opsIncome)
    {
        $editWindowDays = config('operational.expense_edit_window_days');
        $requestDate = Carbon::parse($request->date)->startOfDay();
        $limitDate = now()->subDays($editWindowDays)->startOfDay();
        if ($requestDate->lt($limitDate)) {
            return response()->json([
                'success' => false,
                'message' => __('operational.incomes.edit_window_expired', ['days' => $editWindowDays]),
                'code' => 422,
            ], 422);
        }

        DB::beginTransaction();
        try {
            if ($opsIncome->transferConfirmation->status !== OpsTransferConfirmationStatus::PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => __('operational.incomes.not_pending'),
                    'code' => 422,
                ], 422);
            }

            $companyId = $request->user()->company_id;
            $mandor = $this->subCompanyService->resolveMandor($request->mandor_uuid, $companyId);
            $subCompany = $this->subCompanyService->resolveForAdmin(
                $request->sub_company_uuid,
                $companyId,
                $mandor->id
            );

            $payload = [
                'name' => $request->name,
                'amount' => $request->amount,
                'date' => $request->date,
                'mandor_id' => $mandor->id,
                'sub_company_id' => $subCompany->id,
                'created_by' => $request->user()->id,
                'company_id' => $companyId,
            ];

            if ($request->hasFile('proof_file')) {
                $payload['proof_file'] = $this->fileService->storeProof($request->file('proof_file'));
                $this->fileService->deleteProof($opsIncome->proof_file);
            }

            $oldData = $opsIncome->only(['name', 'amount', 'date', 'proof_file', 'note']);

            $opsIncome->update($payload);

            OpsEditLog::create([
                'loggable_type' => 'ops_incomes',
                'loggable_id' => $opsIncome->id,
                'reason' => $request->reason ?? '-',
                'old_data' => $oldData,
                'new_data' => $opsIncome->only(['name', 'amount', 'date', 'proof_file', 'note']),
                'edited_by' => $request->user()->id,
                'company_id' => $companyId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('operational.incomes.updated'),
                'data' => new OpsIncomeResource(
                    $opsIncome->load(['mandor', 'subCompany', 'createdBy', 'transferConfirmation'])
                ),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(OpsIncome $opsIncome)
    {
        DB::beginTransaction();
        try {
            if ($opsIncome->transferConfirmation->status !== OpsTransferConfirmationStatus::PENDING) {
                return response()->json([
                    'success' => false,
                    'message' => __('operational.incomes.not_pending'),
                    'code' => 422,
                ], 422);
            }

            if ($opsIncome->proof_file) {
                $this->fileService->deleteProof($opsIncome->proof_file);
            }

            $opsIncome->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('operational.incomes.deleted'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
