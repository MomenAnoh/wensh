<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\NotificationPreferenceRepository;

class NotificationPreferenceService
{
    public function __construct(private readonly NotificationPreferenceRepository $repository) {}

    public function update(User $user, string $preference, bool $enabled): User
    {
        return $this->repository->update($user, $preference, $enabled);
    }
}
