<?php

namespace App\Repositories\User;

use App\Models\User;

class ProfileRepository
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(User $user, array $attributes): User
    {
        $user->fill($attributes);
        $user->save();

        return $user->refresh();
    }

    public function skip(User $user): User
    {
        $user->forceFill(['profile_skipped_at' => now()])->save();

        return $user->refresh();
    }
}
