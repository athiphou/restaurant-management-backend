<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\FoodVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodVariantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => FoodVariant::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([]);

        $model = FoodVariant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = FoodVariant::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = FoodVariant::findOrFail($id);

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
        $model = FoodVariant::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
