<?php

namespace App\DTOS;

use App\Interfaces\DTOInterface;

class StoreItemsSaleDTO implements DTOInterface
{
    public function __construct(
        public readonly int $sale_id,
        public readonly int $product_id,
        public readonly int $quantity,
        public readonly float $unit_price,
        public readonly ?float $discount = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            sale_id: data_get($data, 'sale_id'),
            product_id: data_get($data, 'product_id'),
            quantity: data_get($data, 'quantity'),
            unit_price: data_get($data, 'unit_price'),
            discount: data_get($data, 'discount'),
        );
    }
}
