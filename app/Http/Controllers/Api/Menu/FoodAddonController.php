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
        $addons = FoodAddon::with('food')->get();

        return response()->json([
            'success' => true,
            'data' => $addons,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'food_id' => 'required|exists:foods,id',
            'name' => 'required|string|max:255',
            'is_required' => 'boolean',
            'max_items' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $addon = FoodAddon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food addon created successfully',
            'data' => $addon->load('food'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $addon = FoodAddon::with(['food', 'items'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $addon,
        ]);
    }

    public function items(string $id): JsonResponse
    {
        $addon = FoodAddon::with('items')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $addon->items,
        ]);
    }

    public function toggleAvailability(string $id): JsonResponse
    {
        $addon = FoodAddon::findOrFail($id);
        $addon->is_available = !$addon->is_available;
        $addon->save();

        return response()->json([
            'success' => true,
            'message' => $addon->is_available ? 'Add-on is now available' : 'Add-on is now unavailable',
            'data' => $addon->load('food'),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $addon = FoodAddon::findOrFail($id);

        $validated = $request->validate([
            'food_id' => 'sometimes|required|exists:foods,id',
            'name' => 'sometimes|required|string|max:255',
            'is_required' => 'boolean',
            'max_items' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $addon->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food addon updated successfully',
            'data' => $addon->load('food'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $addon = FoodAddon::findOrFail($id);
        $addon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food addon deleted successfully',
        ]);
    }
}