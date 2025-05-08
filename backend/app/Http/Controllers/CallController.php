<?php

namespace App\Http\Controllers;

use App\Models\Call;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            Log::info('Authenticated user:', ['user' => $user]);

            /** @var \App\Models\User $user **/
            Log::info('User roles:', ['roles' => $user->getRoleNames()]);

            // Only agents can see their own calls, supervisors can see all calls
            $calls = Call::with('user')
                ->when($user->role === 'agent', fn($query) => $query->where('user_id', $user->id))
                ->when($user->role === 'supervisor', fn($query) => $query->whereHas('user', fn($q) => $q->where('role', 'agent')))
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $calls
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch calls',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'agent') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only agents can record calls.'
                ], 403);
            }

            $validated = $request->validate([
                'time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:1',
                'subject' => 'required|string|max:255',
            ]);

            $call = $request->user()->calls()->create($validated);

            return response()->json([
                'success' => true,
                'data' => $call,
                'message' => 'Call recorded successfully.'
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
                'message' => 'Failed to create call',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Call $call)
    {
        try {
            $user = Auth::user();

            if ($user->role === 'agent' && $call->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this call'
                ], 403);
            }

            // Supervisors can view all calls
            return response()->json([
                'success' => true,
                'data' => $call->load('user')
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this call'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch call',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Call $call)
    {
        try {
            $user = $request->user();

            if ($user->role !== 'agent' || $call->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the agent who created this call can update it.'
                ], 403);
            }

            $validated = $request->validate([
                'time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:1',
                'subject' => 'required|string|max:255',
            ]);

            $call->update($validated);

            return response()->json([
                'success' => true,
                'data' => $call,
                'message' => 'Call updated successfully.'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this call'
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update call',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Call $call)
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'agent' || $call->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the agent who created this call can delete it.'
                ], 403);
            }

            $call->delete();

            return response()->json([
                'success' => true,
                'message' => 'Call deleted successfully.'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this call'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete call',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function checkForTicket(Call $call)
    {
        try {
            $user = Auth::user();

            // Agents can only check for their own calls
            if ($user->role === 'agent' && $call->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to check ticket for this call'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'has_ticket' => $call->ticket !== null,
                'call' => $call,
                'message' => $call->ticket ? 'Call has ticket' : 'Call has no ticket'
            ]);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to check this call'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check call for ticket',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
