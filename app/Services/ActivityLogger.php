<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function log(string $action, ?object $entity = null, ?string $description = null, ?Request $request = null): void
    {
        ActivityLog::create(['user_id' => auth()->id(), 'action' => $action, 'entity_type' => $entity ? $entity::class : null, 'entity_id' => $entity?->getKey(), 'description' => $description, 'ip_address' => $request?->ip() ?? request()->ip()]);
    }
}
