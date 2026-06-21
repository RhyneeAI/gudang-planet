<?php

namespace App\Services\Absence;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AbsFileService
{
    public function storePhoto(UploadedFile $file, string $prefix): string
    {
        $directory = config('absence.photo_directory') . '/' . $prefix;

        return $file->store($directory, config('absence.photo_disk'));
    }

    public function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk(config('absence.photo_disk'))->url($path);
    }
}
