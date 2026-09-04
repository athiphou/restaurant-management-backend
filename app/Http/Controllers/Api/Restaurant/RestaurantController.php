<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Restaurant::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $restaurant = Restaurant::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant created successfully',
            'data' => $restaurant,
        ], 201);
    }

    public function show(Restaurant $restaurant): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $restaurant,
        ]);
    }

    public function update(Request $request, Restaurant $restaurant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $restaurant->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $restaurant,
        ]);
    }

    public function destroy(Restaurant $restaurant): JsonResponse
    {
        $restaurant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
