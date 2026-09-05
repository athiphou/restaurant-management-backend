<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\RecipeItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeItemController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => RecipeItem::with(['recipe', 'ingredient'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'recipe_id' => 'required|exists:recipes,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity_required' => 'required|numeric|min:0',
        ]);

        $model = RecipeItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['recipe', 'ingredient']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = RecipeItem::with(['recipe', 'ingredient'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = RecipeItem::findOrFail($id);

        $validated = $request->validate([
            'recipe_id' => 'sometimes|required|exists:recipes,id',
            'ingredient_id' => 'sometimes|required|exists:ingredients,id',
            'quantity_required' => 'sometimes|required|numeric|min:0',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['recipe', 'ingredient']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = RecipeItem::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
