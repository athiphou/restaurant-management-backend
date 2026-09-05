<?php

namespace App\Http\Controllers\Api\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\RestaurantBranch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantBranchController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => RestaurantBranch::all(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $branch = RestaurantBranch::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'data' => $branch,
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $model = RestaurantBranch::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $model,
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $branch = RestaurantBranch::findOrFail($id);

        $branch->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Updated successfully',
            'data' => $branch,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $model = RestaurantBranch::findOrFail($id);
        $model->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully',
        ]);
    }
}
