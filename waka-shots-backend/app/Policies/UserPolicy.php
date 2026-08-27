<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, User $model): bool
    {
        if (! $this->isAdmin($user) || $user->is($model)) {
            return false;
        }

        return ! ($model->role === UserRole::Admin && User::query()->where('role', UserRole::Admin->value)->count() <= 1);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin || $user->role === UserRole::Admin->value;
    }
}
