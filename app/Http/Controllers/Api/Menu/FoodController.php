<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\Food;
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
        $food = Food::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $food,
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