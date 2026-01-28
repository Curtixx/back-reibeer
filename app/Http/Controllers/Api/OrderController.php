<?php

namespace App\Http\Controllers\Api;

use App\DTOS\StoreOrderDTO;
use App\DTOS\UpdateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(IndexOrderRequest $request)
    {
        try {
            $query = Order::query()->where('is_active', true)->with('orderProducts.product');

            if ($request->filled('number')) {
                $query->where('number', 'like', '%'.$request->input('number').'%');
            }

            if ($request->filled('responsible_name')) {
                $query->where('responsible_name', 'like', '%'.$request->input('responsible_name').'%');
            }

            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            if ($request->filled('product_id')) {
                $query->whereHas('orderProducts', function ($q) use ($request) {
                    $q->where('product_id', $request->input('product_id'));
                });
            }

            $orders = $query->get();

            return response()->json(OrderResource::collection($orders));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao buscar comandas', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $orderDTO = StoreOrderDTO::fromArray($request->validated());
            $order = $this->orderService->createOrder($orderDTO);

            return response()->json(new OrderResource($order), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao criar comanda', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        try {
            $order->load('orderProducts.product');

            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao buscar comanda', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $orderDTO = UpdateOrderDTO::fromArray($request->validated());
            $order = $this->orderService->updateOrder($order, $orderDTO);

            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao atualizar comanda', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        try {
            $this->orderService->deleteOrder($order);

            return response()->json(['message' => 'Comanda excluída com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao excluir comanda', 'message' => $e->getMessage()], 500);
        }
    }
}
