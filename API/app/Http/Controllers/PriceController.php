<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index(): JsonResponse
    {
        $prices = Price::query()
            ->with('category')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $prices,
        ]);
    }

    public function show(Price $price): JsonResponse
    {
        return response()->json([
            'data' => $price->load('category'),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:price_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $price = Price::query()->create($validated);

        return response()->json([
            'message' => 'Price created.',
            'data' => $price->load('category'),
        ], 201);
    }

    public function update(Request $request, Price $price): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:price_categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
        ]);

        $price->fill($validated);
        $price->save();

        return response()->json([
            'message' => 'Price updated.',
            'data' => $price->fresh()->load('category'),
        ]);
    }

    public function destroy(Price $price): JsonResponse
    {
        $price->delete();

        return response()->json([
            'message' => 'Price deleted.',
        ]);
    }
}
