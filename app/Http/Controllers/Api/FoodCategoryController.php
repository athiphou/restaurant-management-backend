<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = FoodCategory::orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category = FoodCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food category created successfully',
            'data' => $category,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    public function foods(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);

        $foods = $category->foods()
            ->with(['variants', 'addons.items'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $foods,
        ]);
    }

    public function toggleActive(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => $category->is_active ? 'Category activated' : 'Category hidden',
            'data' => $category,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $category = FoodCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food category deleted successfully',
        ]);
    }
}
