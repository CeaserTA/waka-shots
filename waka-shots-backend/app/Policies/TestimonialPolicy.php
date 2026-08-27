<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Testimonial;
use App\Models\User;

class TestimonialPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, Testimonial $testimonial): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Testimonial $testimonial): bool
    {
        return $this->isAdmin($user);
    }

    private function isAdmin(User $user): bool
    {
        return $user->role === UserRole::Admin || $user->role === UserRole::Admin->value;
    }
}