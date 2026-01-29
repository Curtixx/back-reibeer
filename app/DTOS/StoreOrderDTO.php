<?php

namespace App\DTOS;

use App\Interfaces\DTOInterface;
use App\OrderStatus;

class StoreOrderDTO implements DTOInterface
{
    public function __construct(
        public readonly string $number,
        public readonly string $responsible_name,
        public readonly ?string $status = OrderStatus::Open->value,
        public readonly array $products,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            number: data_get($data, 'number'),
            responsible_name: data_get($data, 'responsible_name'),
            status: data_get($data, 'status', OrderStatus::Open->value),
            products: data_get($data, 'products', []),
        );
    }
}
