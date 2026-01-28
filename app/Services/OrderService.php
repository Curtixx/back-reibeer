<?php

namespace App\Services;

use App\DTOS\StoreOrderDTO;
use App\DTOS\UpdateOrderDTO;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(StoreOrderDTO $orderDTO): Order
    {
        return DB::transaction(function () use ($orderDTO) {
            $order = Order::create([
                'number' => $orderDTO->number,
                'responsible_name' => $orderDTO->responsible_name,
                'status' => $orderDTO->status,
                'is_active' => true,
            ]);

            $products = collect($orderDTO->products)->mapWithKeys(function ($item) {
                return [$item['id'] => ['quantity' => $item['quantity']]];
            });

            $order->products()->attach($products);

            return $order->load('orderProducts.product');
        });
    }

    public function updateOrder(Order $order, UpdateOrderDTO $orderDTO): Order
    {
        return DB::transaction(function () use ($order, $orderDTO) {
            $updateData = [];

            if ($orderDTO->number !== null) {
                $updateData['number'] = $orderDTO->number;
            }

            if ($orderDTO->responsible_name !== null) {
                $updateData['responsible_name'] = $orderDTO->responsible_name;
            }

            if ($orderDTO->status !== null) {
                $updateData['status'] = $orderDTO->status;
            }

            if (! empty($updateData)) {
                $order->update($updateData);
            }

            if ($orderDTO->products !== null) {
                $products = collect($orderDTO->products)->mapWithKeys(function ($item) {
                    return [$item['id'] => ['quantity' => $item['quantity']]];
                });

                $order->products()->sync($products);
            }

            return $order->fresh()->load('orderProducts.product');
        });
    }

    public function deleteOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['is_active' => false]);
        });
    }
}
