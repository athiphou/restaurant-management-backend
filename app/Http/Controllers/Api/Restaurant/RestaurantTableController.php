<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RestaurantTableController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => RestaurantTable::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:restaurant_branches,id',
            'table_number' => 'required|string|max:50',
            'capacity' => 'required|integer|min:1',
            'status' => ['nullable', 'string', Rule::in(['available', 'occupied', 'reserved'])],
        ]);

        $table = RestaurantTable::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully',
            'data' => $table,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = RestaurantTable::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
       $validated = $request->validate([
            'table_number' => 'sometimes|required|string|max:50',
            'capacity' => 'sometimes|required|integer|min:1',
            'status' => ['sometimes', 'required', 'string', Rule::in(['available', 'occupied', 'reserved'])],
        ]);

        $table = RestaurantTable::findOrFail($id);

        $table->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $table,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = RestaurantTable::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
