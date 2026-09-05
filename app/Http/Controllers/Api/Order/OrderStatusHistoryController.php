<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\OrderStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderStatusHistoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => OrderStatusHistory::with('order')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,completed,cancelled',
            'changed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $model = OrderStatusHistory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load('order'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = OrderStatusHistory::with('order')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = OrderStatusHistory::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'status' => 'sometimes|required|in:pending,confirmed,preparing,ready,out_for_delivery,completed,cancelled',
            'changed_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load('order'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = OrderStatusHistory::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}