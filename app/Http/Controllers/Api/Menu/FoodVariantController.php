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
        $variants = FoodVariant::with('food')->get();

        return response()->json([
            'success' => true,
            'data' => $variants,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $variant = FoodVariant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food variant created successfully',
            'data' => $variant->load('food'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $variant = FoodVariant::with('food')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $variant,
        ]);
    }

    public function toggleAvailability(string $id): JsonResponse
    {
        $variant = FoodVariant::findOrFail($id);
        $variant->is_available = !$variant->is_available;
        $variant->save();

        return response()->json([
            'success' => true,
            'message' => $variant->is_available ? 'Variant is now available' : 'Variant is now unavailable',
            'data' => $variant->load('food'),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $variant = FoodVariant::findOrFail($id);

        $validated = $request->validate([
            'food_id' => 'sometimes|required|exists:foods,id',
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $variant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food variant updated successfully',
            'data' => $variant->load('food'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $variant = FoodVariant::findOrFail($id);
        $variant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food variant deleted successfully',
        ]);
    }
}