<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Employee::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'=>'required|exists:users,id',
            'branch_id' => 'required|exists:restaurant_branches,id',
            'position_id' => 'required|exists:staff_positions,id',
            'hire_date' => 'required|date',
            'salary' => 'required|numeric|min:0',
        ]);
        $employee = Employee::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $employee->load(['user', 'branch', 'position']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = Employee::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = Employee::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'branch_id' => 'sometimes|exists:restaurant_branches,id',
            'position_id' => 'sometimes|exists:staff_positions,id',
            'hire_date' => 'sometimes|date',
            'salary' => 'sometimes|numeric|min:0',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['user', 'branch', 'position']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = Employee::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
