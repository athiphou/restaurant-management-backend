<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Inventory::with(['branch', 'ingredient'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:restaurant_branches,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity' => 'required|numeric|min:0',
        ]);

        $model = Inventory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['branch', 'ingredient']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = Inventory::with(['branch', 'ingredient'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = Inventory::findOrFail($id);

        $validated = $request->validate([
            'branch_id' => 'sometimes|required|exists:restaurant_branches,id',
            'ingredient_id' => 'sometimes|required|exists:ingredients,id',
            'quantity' => 'sometimes|required|numeric|min:0',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['branch', 'ingredient']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = Inventory::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
