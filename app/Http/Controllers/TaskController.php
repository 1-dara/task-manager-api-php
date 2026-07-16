<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TaskController extends Controller
{
    #[OA\Get(
        path: "/api/tasks",
        summary: "List all tasks for the logged-in user",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "List of tasks"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function index(Request $request)
    {
        return $request->user()->tasks()->get();
    }

    #[OA\Post(
        path: "/api/tasks",
        summary: "Create a new task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["title"],
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Learn Laravel"),
                    new OA\Property(property: "description", type: "string", example: "Study Eloquent and routing"),
                    new OA\Property(property: "status", type: "string", example: "pending"),
                    new OA\Property(property: "priority", type: "string", example: "medium"),
                    new OA\Property(property: "due_date", type: "string", format: "date", example: "2026-08-01"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Task created successfully"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,in_progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201);
    }

    #[OA\Get(
        path: "/api/tasks/{id}",
        summary: "Get a single task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 200, description: "Task found"),
            new OA\Response(response: 404, description: "Task not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function show(Request $request, string $id)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return $task;
    }

    #[OA\Put(
        path: "/api/tasks/{id}",
        summary: "Update a task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string", example: "Learn Laravel"),
                    new OA\Property(property: "description", type: "string", example: "Study Eloquent and routing"),
                    new OA\Property(property: "status", type: "string", example: "completed"),
                    new OA\Property(property: "priority", type: "string", example: "high"),
                    new OA\Property(property: "due_date", type: "string", format: "date", example: "2026-08-01"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Task updated successfully"),
            new OA\Response(response: 404, description: "Task not found"),
            new OA\Response(response: 422, description: "Validation error"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function update(Request $request, string $id)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|in:pending,in_progress,completed',
            'priority' => 'sometimes|string|in:low,medium,high',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return $task;
    }

    #[OA\Delete(
        path: "/api/tasks/{id}",
        summary: "Delete a task",
        tags: ["Tasks"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(response: 204, description: "Task deleted successfully"),
            new OA\Response(response: 404, description: "Task not found"),
            new OA\Response(response: 401, description: "Unauthenticated"),
        ]
    )]
    public function destroy(Request $request, string $id)
    {
        $task = $request->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted'], 204);
    }
}
