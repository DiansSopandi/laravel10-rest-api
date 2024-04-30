<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TaskResource::collection(Task::latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());

        return TaskResource::make($task);
    }

    /**
     * Display the specified resource.
     */
    // public function show(Task $task)
    public function show($id)
    {
        // return TaskResource::make($task) 
        $task = Task::find($id);
        return $task ? TaskResource::make($task) : response()->json(['data' => null, 'message' => 'Task not found'], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }

        $task->update($request->validated());

        /**
         * 
         *try {
         *    $existTask = Task::findOrFail($task->id);
         *    $existTask->update($request->validated());
         *} catch (ModelNotFoundException $e) {
         *     return response()->json(['data' => null]);
         *}
         */

        $validated = json_encode($request->validated());
        $message = "Task {$validated} updated successfully";

        Log::info($message);

        return TaskResource::make($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->noContent();
    }
}
