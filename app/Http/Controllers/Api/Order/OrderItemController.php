<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = OrderItem::with([
            'order',
            'food',
            'variant',
        ])->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'food_id' => 'required|exists:foods,id',
            'variant_id' => 'nullable|exists:food_variants,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $item = OrderItem::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order item created successfully',
            'data' => $item->load([
                'order',
                'food',
                'variant',
            ]),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $item = OrderItem::with([
            'order',
            'food',
            'variant',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $item = OrderItem::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'food_id' => 'sometimes|required|exists:foods,id',
            'variant_id' => 'nullable|exists:food_variants,id',
            'quantity' => 'sometimes|required|integer|min:1',
            'unit_price' => 'sometimes|required|numeric|min:0',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order item updated successfully',
            'data' => $item->load([
                'order',
                'food',
                'variant',
            ]),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $item = OrderItem::findOrFail($id);

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order item deleted successfully',
        ]);
    }
}