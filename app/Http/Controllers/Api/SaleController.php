<?php

namespace App\Http\Controllers\Api;

use App\DTOS\StoreItemsSaleDTO;
use App\DTOS\StoreSaleDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Services\SaleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleRequest $request, SaleService $saleService)
    {
        try {
            $data = $request->validated();
            $storeSaleDTO = StoreSaleDTO::fromArray([
                'payment_method' => data_get($data, 'pagamento.forma'),
                'total' => data_get($data, 'total'),
                'id_cashier' => data_get($data, 'id_cashier'),

            ]);

            DB::transaction(function () use ($saleService, $storeSaleDTO, $data) {
                $sale = $saleService->createSale($storeSaleDTO);

                foreach (data_get($data, 'items', []) as $item) {
                    $storeItemsSaleDTO = StoreItemsSaleDTO::fromArray([
                        'sale_id' => $sale->id,
                        'product_id' => data_get($item, 'produtoId'),
                        'quantity' => data_get($item, 'quantidade'),
                        'unit_price' => data_get($item, 'precoUnitario'),
                    ]);

                    $saleService->createItemsSale($storeItemsSaleDTO);
                }
            });

            return response()->json(['message' => 'Sale processed successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process sale'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
