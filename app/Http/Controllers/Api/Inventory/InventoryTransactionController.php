<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryTransactionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => InventoryTransaction::with(['inventory', 'supplier'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'transaction_type' => 'required|string|in:restock,usage,waste,adjustment',
            'quantity' => 'required|numeric',
            'unit_cost' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $model = InventoryTransaction::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['inventory', 'supplier']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = InventoryTransaction::with(['inventory', 'supplier'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = InventoryTransaction::findOrFail($id);

        $validated = $request->validate([
            'inventory_id' => 'sometimes|required|exists:inventory,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'transaction_type' => 'sometimes|required|string|in:restock,usage,waste,adjustment',
            'quantity' => 'sometimes|required|numeric',
            'unit_cost' => 'sometimes|required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['inventory', 'supplier']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = InventoryTransaction::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
