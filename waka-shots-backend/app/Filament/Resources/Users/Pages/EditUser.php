<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var User $user */
        $user = $this->record;
        $newRole = $data['role'] instanceof UserRole ? $data['role'] : UserRole::tryFrom((string) ($data['role'] ?? ''));

        if ($user->role === UserRole::Admin && $newRole !== UserRole::Admin && User::query()->where('role', UserRole::Admin->value)->count() <= 1) {
            throw ValidationException::withMessages([
                'role' => 'The last administrator cannot be demoted.',
            ]);
        }

        return $data;
    }
}
