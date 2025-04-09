<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * Get all tasks for the authenticated user or all tasks
     *
     * @param bool $allTasks Whether to get all tasks or just the user's tasks
     * @return Collection
     */
    public function getAllTasks(bool $allTasks = false): Collection
    {
        $query = Task::with('user');

        if (!$allTasks) {
            $query->where('user_id', Auth::id());
        }

        return $query->get();
    }

    /**
     * Get a specific task by ID
     *
     * @param int $id
     * @param bool $checkOwnership Whether to check if the task belongs to the authenticated user
     * @return Task
     */
    public function getTaskById(int $id, bool $checkOwnership = false): Task
    {
        $query = Task::with('user');

        if ($checkOwnership) {
            $query->where('user_id', Auth::id());
        }

        return $query->findOrFail($id);
    }

    /**
     * Create a new task
     *
     * @param array $data
     * @return Task
     */
    public function createTask(array $data): Task
    {
        $data['user_id'] = Auth::id();
        return Task::create($data);
    }

    /**
     * Update an existing task
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh();
    }

    /**
     * Delete a task
     *
     * @param Task $task
     * @return bool
     */
    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }

    /**
     * Check if a task belongs to the authenticated user
     *
     * @param Task $task
     * @return bool
     */
    public function isOwnedByCurrentUser(Task $task): bool
    {
        return $task->user_id === Auth::id();
    }
}
