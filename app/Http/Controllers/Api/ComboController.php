<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComboRequest;
use App\Http\Requests\UpdateComboRequest;
use App\Http\Resources\ComboResource;
use App\Models\Combo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComboController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $combos = Combo::where('is_active', true)->with('comboProducts.product')->get();
            return response()->json($combos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch combos', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreComboRequest $request)
    {
        try {
            $combo = DB::transaction(function () use ($request) {
                $combo = Combo::create([
                    'name' => $request->validated('name'),
                    'sale_price' => $request->validated('sale_price'),
                    'pix_price' => $request->validated('pix_price'),
                    'is_active' => true,
                ]);

                $products = collect($request->validated('products'))->mapWithKeys(function ($item) {
                    return [$item['id'] => ['quantity' => $item['quantity']]];
                });

                $combo->products()->attach($products);

                return $combo;
            });

            return response()->json(new ComboResource($combo), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create combo', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Combo $combo)
    {
        try {
            $combo->load('comboProducts.product');
            return response()->json(new ComboResource($combo));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch combo', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComboRequest $request, Combo $combo)
    {
        try {
            $combo = DB::transaction(function () use ($request, $combo) {
                $combo->update([
                    'name' => $request->validated('name'),
                    'sale_price' => $request->validated('sale_price'),
                    'pix_price' => $request->validated('pix_price'),
                ]);

                if ($request->validated('products')) {
                    $products = collect($request->validated('products'))->mapWithKeys(function ($item) {
                        return [$item['id'] => ['quantity' => $item['quantity']]];
                    });

                    $combo->products()->sync($products);
                }

                return $combo;
            });

            return response()->json(new ComboResource($combo));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update combo', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Combo $combo)
    {
        try {
            $combo->comboProducts()->delete();
            $combo->delete();

            return response()->json(['message' => 'Combo deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete combo', 'message' => $e->getMessage()], 500);
        }
    }
}
