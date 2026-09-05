<?php

namespace App\Http\Controllers\Api\Menu;

use App\Http\Controllers\Controller;
use App\Models\FoodAddonItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodAddonItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = FoodAddonItem::with('addon')->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'addon_id' => 'required|exists:food_addons,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $item = FoodAddonItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food addon item created successfully',
            'data' => $item->load('addon'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $item = FoodAddonItem::with('addon')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = FoodAddonItem::findOrFail($id);

        $validated = $request->validate([
            'addon_id' => 'sometimes|required|exists:food_addons,id',
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'is_available' => 'boolean',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Food addon item updated successfully',
            'data' => $item->load('addon'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $item = FoodAddonItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Food addon item deleted successfully',
        ]);
    }
}