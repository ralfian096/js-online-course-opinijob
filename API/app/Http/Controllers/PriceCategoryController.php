<?php

namespace App\Http\Controllers;

use App\Models\PriceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = PriceCategory::query()
            ->withCount('prices')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function show(PriceCategory $category): JsonResponse
    {
        $category->loadCount('prices');

        return response()->json([
            'data' => $category,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category = PriceCategory::query()->create($validated);

        return response()->json([
            'message' => 'Price category created.',
            'data' => $category->loadCount('prices'),
        ], 201);
    }

    public function update(Request $request, PriceCategory $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $category->fill($validated);
        $category->save();

        return response()->json([
            'message' => 'Price category updated.',
            'data' => $category->fresh()->loadCount('prices'),
        ]);
    }

    public function destroy(PriceCategory $category): JsonResponse
    {
        if ($category->prices()->exists()) {
            return response()->json([
                'message' => 'Price category still has related pricing data.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Price category deleted.',
        ]);
    }
}
