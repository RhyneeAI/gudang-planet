<?php

namespace App\Services\Operational;

use App\Enums\Role;
use App\Models\Company;
use App\Models\OpsConfiguration;
use App\Models\OpsSubCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OpsSubCompanyService
{
    public const KEY_MAX_SUB_COMPANIES_PER_MANDOR = 'max_sub_companies_per_mandor';

    public function __construct(
        protected OpsWalletService $walletService,
    ) {}

    public function maxSubCompaniesPerMandor(int $companyId): int
    {
        $configured = OpsConfiguration::where('company_id', $companyId)
            ->where('key', self::KEY_MAX_SUB_COMPANIES_PER_MANDOR)
            ->value('value');

        if ($configured !== null) {
            return max(1, (int) $configured);
        }

        return (int) config('operational.max_sub_companies_per_mandor', 10);
    }

    public function countForMandor(int $mandorId): int
    {
        return OpsSubCompany::where('mandor_id', $mandorId)->count();
    }

    public function createDefaultForMandor(User $mandor, ?User $createdBy = null): OpsSubCompany
    {
        if ($mandor->role !== Role::MANDOR) {
            throw new \InvalidArgumentException('User must have MANDOR role.');
        }

        $existing = OpsSubCompany::where('mandor_id', $mandor->id)->first();
        if ($existing) {
            return $existing;
        }

        $limit = $this->maxSubCompaniesPerMandor($mandor->company_id);
        if ($this->countForMandor($mandor->id) >= $limit) {
            throw new \RuntimeException(__('operational.sub_companies.limit_reached', [
                'limit' => $limit,
            ]));
        }

        $company = Company::findOrFail($mandor->company_id);

        $subCompany = OpsSubCompany::create([
            'name' => $company->name,
            'code' => $this->generateUniqueCode($company),
            'address' => $company->address,
            'is_active' => true,
            'mandor_id' => $mandor->id,
            'company_id' => $company->id,
            'created_by' => $createdBy?->id,
        ]);

        $this->walletService->getOrCreateWallet($mandor, $subCompany);

        return $subCompany;
    }

    public function ensureDefaultForMandor(User $mandor, ?User $createdBy = null): OpsSubCompany
    {
        return $this->createDefaultForMandor($mandor, $createdBy);
    }

    protected function generateUniqueCode(Company $company): string
    {
        $sequence = OpsSubCompany::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->count() + 1;

        $code = $company->code . '-' . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);

        while (
            OpsSubCompany::withoutGlobalScopes()
                ->where('company_id', $company->id)
                ->where('code', $code)
                ->exists()
        ) {
            $sequence++;
            $code = $company->code . '-' . str_pad((string) $sequence, 2, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function resolveForMandor(string $uuid, User $mandor): OpsSubCompany
    {
        return OpsSubCompany::where('uuid', $uuid)
            ->where('mandor_id', $mandor->id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function resolveForAdmin(string $uuid, int $companyId, ?int $mandorId = null): OpsSubCompany
    {
        return OpsSubCompany::where('uuid', $uuid)
            ->where('company_id', $companyId)
            ->when($mandorId, fn ($query) => $query->where('mandor_id', $mandorId))
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function resolveMandor(string $mandorUuid, int $companyId): User
    {
        $mandor = User::where('uuid', $mandorUuid)
            ->where('company_id', $companyId)
            ->where('role', Role::MANDOR)
            ->where('is_active', true)
            ->first();

        if (!$mandor) {
            throw new ModelNotFoundException(__('operational.validation.mandor_uuid_not_found'));
        }

        return $mandor;
    }
}
