<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDriver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryDriverController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => DeliveryDriver::with(['user', 'branch'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'required|exists:restaurant_branches,id',
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:20',
            'status' => 'sometimes|in:available,busy,offline',
        ]);

        $model = DeliveryDriver::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $model->load(['user', 'branch']),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = DeliveryDriver::with(['user', 'branch'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $model = DeliveryDriver::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'branch_id' => 'sometimes|exists:restaurant_branches,id',
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:20',
            'status' => 'sometimes|in:available,busy,offline',
        ]);

        $model->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $model->load(['user', 'branch']),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = DeliveryDriver::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
