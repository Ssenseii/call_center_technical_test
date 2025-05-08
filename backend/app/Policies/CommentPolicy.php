<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'supervisor', 'agent']);
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' ||
            $user->id === $comment->user_id ||
            ($user->role === 'supervisor' && $comment->user->role === 'agent');
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'agent', 'supervisor']);
    }

    public function update(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' || $user->id === $comment->user_id;
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->role === 'admin' ||
            $user->id === $comment->user_id ||
            ($user->role === 'supervisor' && $comment->user->role === 'agent');
    }
}
