<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = $this->taskService->getAllTasks(true);

        return response()->json([
            'status' => 'success',
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'nullable|in:pending,in-progress,completed',
        ]);

        $task = $this->taskService->createTask($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'data' => $task,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = $this->taskService->getTaskById($id);

        return response()->json([
            'status' => 'success',
            'data' => $task,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        if (!$this->taskService->isOwnedByCurrentUser($task)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access',
            ]);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:pending,in-progress,completed',
        ]);

        $updatedTask = $this->taskService->updateTask($task, $validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'data' => $updatedTask,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if (!$this->taskService->isOwnedByCurrentUser($task)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access',
            ]);
        }

        $this->taskService->deleteTask($task);

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully',
        ]);
    }
}
