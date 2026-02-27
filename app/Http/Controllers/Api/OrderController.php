<?php

namespace App\Http\Controllers\Api;

use App\DTOS\AddProductsToOrderDTO;
use App\DTOS\RemoveProductsFromOrderDTO;
use App\DTOS\StoreOrderDTO;
use App\DTOS\UpdateOrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductsToOrderRequest;
use App\Http\Requests\IndexOrderRequest;
use App\Http\Requests\RemoveProductsFromOrderRequest;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    /**
     * Clear all orders cache.
     */
    private function clearOrdersCache(): void
    {
        Cache::tags(['orders'])->flush();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexOrderRequest $request): AnonymousResourceCollection|JsonResponse
    {
        try {
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 15);
            $filters = $request->only(['number', 'responsible_name', 'status', 'product_id']);
            $cacheKey = 'orders_page:'.$page.':per_page:'.$perPage.':filters:'.md5(serialize($filters));

            $orders = Cache::tags(['orders'])->remember($cacheKey, 600, function () use ($request, $perPage) {
                $query = Order::query()->where('is_active', true)->with('orderProducts.product');

                if ($request->filled('ids')) {
                    $query->whereIn('id', $request->input('ids'));
                }

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

                return $query->paginate($perPage);
            });

            return OrderResource::collection($orders);
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

            $this->clearOrdersCache();

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

            $this->clearOrdersCache();

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

            $this->clearOrdersCache();

            return response()->json(['message' => 'Comanda excluída com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao excluir comanda', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Add products to an order.
     */
    public function addProducts(AddProductsToOrderRequest $request, Order $order)
    {
        try {
            $productsDTO = AddProductsToOrderDTO::fromArray($request->validated());
            $order = $this->orderService->addProductsToOrder($order, $productsDTO);

            $this->clearOrdersCache();

            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao adicionar produtos à comanda', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove products from an order.
     */
    public function removeProducts(RemoveProductsFromOrderRequest $request, Order $order)
    {
        try {
            $productsDTO = RemoveProductsFromOrderDTO::fromArray($request->validated());
            $order = $this->orderService->removeProductsFromOrder($order, $productsDTO);

            $this->clearOrdersCache();

            return response()->json(new OrderResource($order));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha ao remover produtos da comanda', 'message' => $e->getMessage()], 500);
        }
    }
}
