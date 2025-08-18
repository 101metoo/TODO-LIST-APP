<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TodoController extends Controller
{
    /** List todos */
    public function index(Request $request): Response
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

        return Inertia::render('Todos/Index', [
            'todos' => $todos,
            'filters' => $request->only(['status', 'priority', 'search']),
            'stats' => [
                'total' => auth()->user()->todos()->count(),
                'pending' => auth()->user()->todos()->pending()->count(),
                'in_progress' => auth()->user()->todos()->inProgress()->count(),
                'completed' => auth()->user()->todos()->completed()->count(),
                'overdue' => auth()->user()->todos()->overdue()->count(),
            ]
        ]);
    }

    /** Unused */
    public function create()
    {
        //
    }

    /** Create todo */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date|after:today',
        ]);

        $todo = auth()->user()->todos()->create($validated);

        return redirect()->back()->with('success', 'Todo created successfully!');
    }

    /** Unused */
    public function show(string $id)
    {
        //
    }

    /** Unused */
    public function edit(string $id)
    {
        //
    }

    /** Update todo */
    public function update(Request $request, Todo $todo)
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => 'nullable|date',
        ]);

        $todo->update($validated);

        return redirect()->back()->with('success', 'Todo updated successfully!');
    }

    /** Delete todo */
    public function destroy(Todo $todo)
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $todo->delete();

        return redirect()->back()->with('success', 'Todo deleted successfully!');
    }

    /** Toggle status */
    public function toggleStatus(Todo $todo)
    {
        // Authorize owner
        if ($todo->user_id !== auth()->id()) {
            abort(403);
        }

        $newStatus = $todo->status === 'completed' ? 'pending' : 'completed';
        $todo->update(['status' => $newStatus]);

        return redirect()->back()->with('success', 'Todo status updated!');
    }
}
