<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\FoodAddon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodAddonController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => FoodAddon::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([]);

        $model = FoodAddon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = FoodAddon::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = FoodAddon::findOrFail($id);

        $validated = $request->validate([]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = FoodAddon::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
