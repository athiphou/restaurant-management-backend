<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected function withRelations(): array
    {
        return [
            'branch',
            'table',
            'customer',
            'promotion',
            'items.food',
            'items.variant',
            'items.addons.addonItem',
            'statusHistory',
            'payments',
            'delivery.deliveryDriver',
            'delivery.customerAddress',
        ];
    }

    public function index(): JsonResponse
    {
        $orders = Order::with($this->withRelations())->get();

        return response()->json([
            'success' => true,
            'data' => $orders,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:restaurant_branches,id',
            'table_id' => 'nullable|exists:restaurant_tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'promotion_id' => 'nullable|exists:promotions,id',

            'order_number' => 'required|string|max:50|unique:orders,order_number',

            'order_type' => 'required|in:dine_in,takeaway,delivery',

            'status' => 'nullable|in:pending,confirmed,preparing,ready,out_for_delivery,completed,cancelled',

            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',

            'notes' => 'nullable|string',
        ]);

        $order = Order::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $order->load($this->withRelations()),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $order = Order::with($this->withRelations())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

public function update(Request $request, string $id): JsonResponse
{
    $order = Order::findOrFail($id);

    $validated = $request->validate([
        'branch_id' => 'sometimes|required|exists:restaurant_branches,id',
        'table_id' => 'nullable|exists:restaurant_tables,id',
        'customer_id' => 'nullable|exists:customers,id',
        'promotion_id' => 'nullable|exists:promotions,id',

        'order_number' => 'sometimes|required|string|max:50|unique:orders,order_number,' . $id,

        'order_type' => 'sometimes|required|in:dine_in,takeaway,delivery',

        'status' => 'sometimes|required|in:pending,confirmed,preparing,ready,out_for_delivery,completed,cancelled',

        'subtotal' => 'sometimes|required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0',
        'tax' => 'nullable|numeric|min:0',
        'service_charge' => 'nullable|numeric|min:0',
        'delivery_fee' => 'nullable|numeric|min:0',
        'grand_total' => 'sometimes|required|numeric|min:0',

        'notes' => 'nullable|string',
    ]);

    $order->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Order updated successfully',
        'data' => $order->load($this->withRelations()),
    ]);
}

    public function destroy(string $id): JsonResponse
    {
        $order = Order::findOrFail($id);

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }
}