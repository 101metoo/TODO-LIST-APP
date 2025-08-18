<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TodoApiController extends Controller
{
    /** List todos (API) */
    public function index(Request $request): JsonResponse
    {
        $query = auth()->user()->todos();

        // Filter: status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter: priority
        if ($request->has('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // Filter: title contains
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $todos = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $todos,
            'message' => 'Todos retrieved successfully'
        ]);
    }

    /** Create todo (API) */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date|after:today',
        ]);

        $todo = auth()->user()->todos()->create($validated);

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo created successfully'
        ], 201);
    }

    /** Show todo (API) */
    public function show(Todo $todo): JsonResponse
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo retrieved successfully'
        ]);
    }

    /** Update todo (API) */
    public function update(Request $request, Todo $todo): JsonResponse
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        $todo->update($validated);

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo updated successfully'
        ]);
    }

    /** Delete todo (API) */
    public function destroy(Todo $todo): JsonResponse
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todo deleted successfully'
        ]);
    }

    /** Toggle status (API) */
    public function toggleStatus(Todo $todo): JsonResponse
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $newStatus = $todo->status === 'completed' ? 'pending' : 'completed';
        $todo->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo status updated successfully'
        ]);
    }

    /** Stats (API) */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => auth()->user()->todos()->count(),
            'pending' => auth()->user()->todos()->pending()->count(),
            'in_progress' => auth()->user()->todos()->inProgress()->count(),
            'completed' => auth()->user()->todos()->completed()->count(),
            'overdue' => auth()->user()->todos()->overdue()->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Todo statistics retrieved successfully'
        ]);
    }
}
