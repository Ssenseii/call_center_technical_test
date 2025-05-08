<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $user = Auth::user();

            $tickets = Ticket::with(['user', 'call'])
                ->when($user->role === 'agent', fn($query) => $query->where('user_id', $user->id))
                ->when($user->role === 'supervisor', fn($query) => $query->whereHas('user', fn($q) => $q->where('role', 'agent')))
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $tickets
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tickets',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {

        $user = Auth::user();


        try {
            /** @var \App\Models\User $user **/
            $calls = $user->calls()->doesntHave('ticket')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'calls' => $calls
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch calls for ticket creation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function createFromCall(Call $call)
    {
        try {
            $this->authorize('view', $call);

            return response()->json([
                'success' => true,
                'data' => $call
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch call for ticket creation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'agent') {
            return response()->json([
                'success' => false,
                'message' => 'Only agents can create tickets.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'sometimes|string|in:open,in_progress,resolved',
                'call_id' => 'nullable|exists:calls,id',
            ]);

            $user = Auth::user();
            $validated['user_id'] = $user->id;
            $validated['status'] = $validated['status'] ?? 'open';

            $ticket = Ticket::create($validated);

            return response()->json([
                'success' => true,
                'data' => $ticket,
                'message' => 'Ticket created successfully.'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Ticket $ticket)
    {
        try {
            $ticket->load('comments.user');

            return response()->json([
                'success' => true,
                'data' => $ticket
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Ticket $ticket)
    {
        try {
            $user = Auth::user();

            /** @var \App\Models\User $user **/
            $calls = $user->calls()
                ->where(function ($query) use ($ticket) {
                    $query->doesntHave('ticket')
                        ->orWhereHas('ticket', fn($q) => $q->where('id', $ticket->id));
                })
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'ticket' => $ticket,
                    'calls' => $calls
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch ticket data for editing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->role !== 'agent') {
            return response()->json([
                'success' => false,
                'message' => 'Only agents can update tickets.'
            ], 403);
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'status' => 'sometimes|string|in:open,in_progress,resolved',
                'call_id' => 'nullable|exists:calls,id',
            ]);

            $validated['call_id'] = $validated['call_id'] ?? null;
            $ticket->update($validated);

            return response()->json([
                'success' => true,
                'data' => $ticket,
                'message' => 'Ticket updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        try {
            $this->authorize('changeStatus', $ticket);

            $validated = $request->validate([
                'status' => 'required|in:open,in_progress,resolved',
            ]);

            $ticket->update($validated);

            return response()->json([
                'success' => true,
                'data' => $ticket,
                'message' => 'Ticket status updated successfully.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update ticket status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Ticket $ticket)
    {

        $user = Auth::user();

        if ($user->role !== 'agent') {
            return response()->json([
                'success' => false,
                'message' => 'Only agents can delete tickets.'
            ], 403);
        }

        try {
            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
