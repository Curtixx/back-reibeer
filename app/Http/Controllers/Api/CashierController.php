<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cashier\CloseCashierRequest;
use App\Http\Requests\Cashier\OpenCashierRequest;
use App\Models\Cashier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function openCashier(OpenCashierRequest $request)
    {
        try {
            $cashier = Cashier::create([
                'initial_amount' => $request->safe()->initial_amount,
                'user_id_open' => Auth::user()->id,
                'opened_at' => now(),
            ]);

            return response()->json($cashier->id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to open cashier', 'message' => $e->getMessage()], 500);
        }
    }

    public function cashierOpen(Request $request)
    {
        try {
            $cashierOpen = Cashier::query()
                ->where('status', 'aberto')
                ->orderBy('opened_at', 'DESC')
                ->first();

            return response()->json($cashierOpen->id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to find cashier open', 'message' => $e->getMessage()], 500);
        }
    }

    public function closeCashier(CloseCashierRequest $request)
    {
        try {
            Cashier::findOrFail($request->safe()->id_cashier)
                ->update([
                    'user_id_close' => Auth::user()->id,
                    'closed_at' => now(),
                    'total_sales' => $request->safe()->total_sales,
                    'status' => 'fechado',
                ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to close chasier', 'message' => $e->getMessage()], 500);
        }
    }
}
