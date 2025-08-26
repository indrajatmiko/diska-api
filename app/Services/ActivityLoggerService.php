<?php
namespace App\Services;
use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLoggerService
{
    public function log(string $eventType, ?Model $subject = null): void
    {
        /** @var \Illuminate\Http\Request $request */
        $request = request();
        
        UserActivity::create([
            'user_id' => $request->user()?->id,
            'event_type' => $eventType,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);
    }
}