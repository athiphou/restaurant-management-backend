<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Payment::with('order')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string|in:cash,credit_card,qr_code',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:pending,paid,failed,refunded',
            'transaction_reference' => 'nullable|string|max:255',
        ]);

        $model = Payment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load('order'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = Payment::with('order')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = Payment::findOrFail($id);

        $validated = $request->validate([
            'order_id' => 'sometimes|required|exists:orders,id',
            'payment_method' => 'sometimes|required|string|in:cash,credit_card,qr_code',
            'amount' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|string|in:pending,paid,failed,refunded',
            'transaction_reference' => 'nullable|string|max:255',
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
        $model = Payment::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
