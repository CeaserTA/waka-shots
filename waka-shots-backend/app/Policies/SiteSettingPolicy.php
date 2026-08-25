<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\SiteSetting;
use App\Models\User;

class SiteSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, SiteSetting $siteSetting): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, SiteSetting $siteSetting): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, SiteSetting $siteSetting): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin || $user->role === UserRole::Admin->value;
    }
}
