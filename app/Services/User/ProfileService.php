<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\ProfileRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProfileService
{
    public function __construct(private readonly ProfileRepository $profileRepository) {}

    /**
     * @param  array{name: string, email?: string|null, emergency_contact_phone?: string|null, profile_photo?: UploadedFile}  $data
     */
    public function update(User $user, array $data): User
    {
        $oldPhotoPath = $user->profile_photo_path;
        $photo = $data['profile_photo'] ?? null;
        unset($data['profile_photo']);

        if ($photo instanceof UploadedFile) {
            $storedPath = $photo->store('profile-photos', 'public');

            if ($storedPath === false) {
                throw new RuntimeException('The profile photo could not be stored.');
            }

            $data['profile_photo_path'] = $storedPath;
        }

        $data['profile_completed_at'] = now();
        $data['profile_skipped_at'] = null;
        $updatedUser = $this->profileRepository->update($user, $data);

        if ($photo instanceof UploadedFile && $oldPhotoPath !== null) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return $updatedUser;
    }

    public function skip(User $user): User
    {
        return $this->profileRepository->skip($user);
    }
}
