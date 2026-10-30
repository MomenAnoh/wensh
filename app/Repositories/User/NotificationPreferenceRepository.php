<?php

namespace App\Repositories\User;

use App\Models\User;

class NotificationPreferenceRepository
{
    public function update(User $user, string $preference, bool $enabled): User
    {
        $user->forceFill([$preference => $enabled])->save();

        return $user->refresh();
    }
}
