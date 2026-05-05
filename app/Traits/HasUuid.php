<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid  
{
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        // Validate UUID format before querying database
        // This prevents PostgreSQL from throwing database errors on invalid UUIDs
        if (!$this->isValidUuid($value)) {
            return null;
        }

        return parent::resolveRouteBinding($value, $field);
    }

    private function isValidUuid($value): bool
    {
        return Str::isUuid($value);
    }

    protected static function bootHasUuid() 
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}