<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffPosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffPositionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => StaffPosition::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'=>'required|string|max:255',
            'base_salary'=>'nullable|numeric|min:0',
        ]);
        $position = StaffPosition::create($validated);

        return response()->json($position, 201);

    }

    public function show(string $id): JsonResponse
    {
        $model = StaffPosition::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $staffPosition = StaffPosition::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'base_salary' => 'nullable|numeric|min:0',
        ]);

        $staffPosition->update($validated);

        return response()->json($staffPosition);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = StaffPosition::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
