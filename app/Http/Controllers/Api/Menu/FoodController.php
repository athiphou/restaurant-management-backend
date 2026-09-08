<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index(): JsonResponse
    {
        $foods = Food::with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $foods,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:food_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'is_available' => 'boolean',
        ]);

        $food = Food::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food created successfully',
            'data' => $food->load('category'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $food = Food::with(['category', 'variants', 'addons.items'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $food,
        ]);
    }

    public function full(string $id): JsonResponse
    {
        $food = Food::with(['category', 'variants', 'addons.items'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $food,
        ]);
    }

    public function variants(string $id): JsonResponse
    {
        $food = Food::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $food->variants()->orderBy('name')->get(),
        ]);
    }

    public function addons(string $id): JsonResponse
    {
        $food = Food::with(['addons.items'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $food->addons,
        ]);
    }

    public function toggleAvailability(string $id): JsonResponse
    {
        $food = Food::findOrFail($id);
        $food->is_available = !$food->is_available;
        $food->save();

        return response()->json([
            'success' => true,
            'message' => $food->is_available ? 'Food is now available' : 'Food is now unavailable',
            'data' => $food->load('category'),
        ]);
    }

    public function tree(): JsonResponse
    {
        $categories = FoodCategory::orderBy('sort_order')
            ->with([
                'foods' => function ($query) {
                    $query->with(['variants', 'addons.items'])->orderBy('name');
                },
            ])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $food = Food::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'sometimes|required|exists:food_categories,id',
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|string|max:500',
            'is_available' => 'boolean',
        ]);

        $food->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food updated successfully',
            'data' => $food->load('category'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $food = Food::findOrFail($id);
        $food->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food deleted successfully',
        ]);
    }
}