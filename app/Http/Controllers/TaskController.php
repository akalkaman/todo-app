<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = Task::query()
            ->with(['user'])
            ->when(
                $request->get('status'),
                function ($query, $value) {
                    $query->where('status', $value);
                }
            )
            ->when(
                $request->get('priority'),
                function ($query, $value) {
                    $query->where('priority', $value);
                }
            )
            ->when(
                $request->get('deadline'),
                function ($query, $value) {
                    $query->whereDate('deadline', $value);
                }
            )
            ->get();

        return $this->sendResponse(
            TaskResource::collection($tasks)
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $task = Task::query()->create($data);

        return $this->sendResponse(
            TaskResource::make($task)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->sendResponse(
            TaskResource::make($task)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $task->update($request->validated());

        return $this->sendSuccess();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return $this->sendSuccess();
    }
}
