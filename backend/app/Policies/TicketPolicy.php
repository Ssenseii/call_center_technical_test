<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'agent']);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin' ||
            $user->id === $ticket->user_id ||
            ($user->role === 'supervisor' && $ticket->user->role === 'agent');
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'agent']);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin' ||
            ($user->id === $ticket->user_id && $user->role === 'agent') ||
            ($user->role === 'supervisor' && $ticket->user->role === 'agent');
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin' ||
            ($user->role === 'supervisor' && $ticket->user->role === 'agent');
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin' ||
            $user->id === $ticket->user_id ||
            $user->role === 'supervisor';
    }
}
