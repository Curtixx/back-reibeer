<?php

namespace App\Http\Controllers\Api;

use App\CashierStatus;
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
                'initial_amount' => $request->validated()['initial_amount'],
                'user_id_open' => Auth::user()->id,
                'opened_at' => now(),
            ]);

            return response()->json(['id' => $cashier->id, 'message' => 'Caixa aberto com sucesso'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao abrir caixa', 'message' => $e->getMessage()], 500);
        }
    }

    public function cashierOpened(Request $request)
    {
        try {
            $cashierOpen = Cashier::query()
                ->where('status', CashierStatus::Open)
                ->orderBy('opened_at', 'DESC')
                ->first();

            if (! $cashierOpen) {
                return response()->json(['error' => 'Nenhum caixa aberto encontrado'], 404);
            }

            return response()->json(['id' => $cashierOpen->id], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar caixa aberto', 'message' => $e->getMessage()], 500);
        }
    }

    public function closeCashier(CloseCashierRequest $request)
    {
        try {
            $cashier = Cashier::findOrFail($request->validated()['id_cashier']);

            $cashier->update([
                'user_id_close' => Auth::user()->id,
                'closed_at' => now(),
                'total_sales' => $request->validated()['total_sales'],
                'status' => CashierStatus::Closed,
            ]);

            return response()->json(['message' => 'Caixa fechado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao fechar caixa', 'message' => $e->getMessage()], 500);
        }
    }
}
