<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class ActivityService
{
    public function log(
        Model $subject,
        string $action,
        ?Model $target,
        ?Model $actor,
        ?array $meta = null
    ): Activity {
        return Activity::create([
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'actor_id' => $actor?->getKey(),
            'action' => $action,
            'target_type' => $target?->getMorphClass(),
            'target_id' => $target?->getKey(),
            'meta' => $meta,
            'created_at' => now(),
        ]);
    }
}
