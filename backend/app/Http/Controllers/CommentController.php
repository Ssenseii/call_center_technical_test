<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{


    public function store(Request $request, Ticket $ticket)
    {

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        try {
            $validated = $request->validate([
                'content' => 'required|string|max:2000',
            ]);

            $comment = $ticket->comments()->create([
                'user_id' => Auth::id(),
                'content' => $validated['content'],
            ]);

            // Load the user relationship
            $comment->load('user');

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Comment added successfully.',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(Request $request, Comment $comment)
    {
        try {

            $validated = $request->validate([
                'content' => 'required|string|max:2000',
            ]);

            $comment->update($validated);

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Comment updated successfully.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Comment $comment)
    {
        try {

            $ticket = $comment->ticket;
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
