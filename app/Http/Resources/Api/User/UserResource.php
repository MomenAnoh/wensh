<?php

namespace App\Http\Resources\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at?->toISOString(),
            'name' => $this->name,
            'email' => $this->email,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'profile_photo_url' => $this->profile_photo_path === null
                ? null
                : Storage::disk('public')->url($this->profile_photo_path),
            'profile_status' => $this->profile_completed_at !== null
                ? 'completed'
                : ($this->profile_skipped_at !== null ? 'skipped' : 'pending'),
            'notification_preferences' => [
                'requests' => $this->requests_notifications_enabled,
                'offers' => $this->offers_notifications_enabled,
            ],
        ];
    }
}
