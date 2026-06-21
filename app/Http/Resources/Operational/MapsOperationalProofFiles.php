<?php

namespace App\Http\Resources\Operational;

use App\Services\Operational\OpsFileService;

trait MapsOperationalProofFiles
{
    protected function mapProofFiles(OpsFileService $fileService): array
    {
        return [
            'proof_files' => $fileService->urls($this->proof_files ?? []),
        ];
    }
}
