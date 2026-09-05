<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Recipe::with(['food', 'variant', 'items.ingredient'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'variant_id' => 'nullable|exists:food_variants,id',
            'instructions' => 'nullable|string',
        ]);

        $model = Recipe::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['food', 'variant']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = Recipe::with(['food', 'variant', 'items.ingredient'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = Recipe::findOrFail($id);

        $validated = $request->validate([
            'food_id' => 'sometimes|required|exists:foods,id',
            'variant_id' => 'nullable|exists:food_variants,id',
            'instructions' => 'nullable|string',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['food', 'variant']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = Recipe::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
