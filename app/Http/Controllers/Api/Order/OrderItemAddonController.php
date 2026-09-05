<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\OrderItemAddon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemAddonController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrderItemAddon::with(['orderItem', 'addonItem'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'addon_item_id' => 'required|exists:food_addon_items,id',
            'price' => 'required|numeric|min:0',
        ]);

        $model = OrderItemAddon::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['orderItem', 'addonItem']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = OrderItemAddon::with(['orderItem', 'addonItem'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = OrderItemAddon::findOrFail($id);

        $validated = $request->validate([
            'order_item_id' => 'sometimes|required|exists:order_items,id',
            'addon_item_id' => 'sometimes|required|exists:food_addon_items,id',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['orderItem', 'addonItem']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = OrderItemAddon::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}