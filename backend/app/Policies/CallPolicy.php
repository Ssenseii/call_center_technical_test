<?php

namespace App\Policies;

use App\Models\Call;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CallPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'agent']);
    }

    public function view(User $user, Call $call): bool
    {
        return $user->role === 'admin' ||
            $user->id === $call->user_id ||
            ($user->role === 'supervisor' && $call->user->role === 'agent');
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'agent']);
    }

    public function update(User $user, Call $call): bool
    {
        return $user->role === 'admin' ||
            ($user->id === $call->user_id && $user->role === 'agent') ||
            ($user->role === 'supervisor' && $call->user->role === 'agent');
    }

    public function delete(User $user, Call $call): bool
    {
        return $user->role === 'admin' ||
            ($user->role === 'supervisor' && $call->user->role === 'agent');
    }
}
