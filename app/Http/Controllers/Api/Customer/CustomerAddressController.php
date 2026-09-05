<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerAddressController extends Controller
{
    public function index(): JsonResponse
    {
        $addresses = CustomerAddress::with('customer')->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'label' => 'nullable|string|max:100',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        $address = CustomerAddress::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer address created successfully',
            'data' => $address->load('customer'),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $address = CustomerAddress::with('customer')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $address = CustomerAddress::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'sometimes|required|exists:customers,id',
            'label' => 'nullable|string|max:100',
            'address' => 'sometimes|required|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_default' => 'boolean',
        ]);

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Customer address updated successfully',
            'data' => $address->load('customer'),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $address = CustomerAddress::findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer address deleted successfully',
        ]);
    }
}