<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    protected function withRelations(): array
    {
        return [
            'order.branch',
            'order.customer',
            'order.items.food',
            'deliveryDriver.user',
            'customerAddress',
        ];
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Delivery::with($this->withRelations())->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_driver_id' => 'nullable|exists:delivery_drivers,id',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
            'status' => 'nullable|in:pending,assigned,picked_up,out_for_delivery,delivered,failed',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $model = Delivery::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load($this->withRelations()),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = Delivery::with($this->withRelations())->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = Delivery::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'delivery_driver_id' => 'nullable|exists:delivery_drivers,id',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
            'status' => 'sometimes|required|in:pending,assigned,picked_up,out_for_delivery,delivered,failed',
            'delivery_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load($this->withRelations()),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = Delivery::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}