<?php

namespace App\Http\Controllers\Api\Role;

use App\Http\Controllers\Controller;
use App\Models\UserRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UserRole::with(['user', 'role'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $exists = UserRole::where('user_id', $validated['user_id'])
            ->where('role_id', $validated['role_id'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This role is already assigned to the user.',
            ], 422);
        }

        $model = UserRole::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['user', 'role']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = UserRole::with(['user', 'role'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = UserRole::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'role_id' => 'sometimes|required|exists:roles,id',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['user', 'role']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = UserRole::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}