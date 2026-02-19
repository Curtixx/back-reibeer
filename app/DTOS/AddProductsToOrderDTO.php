<?php

namespace App\DTOS;

use App\Interfaces\DTOInterface;

class AddProductsToOrderDTO implements DTOInterface
{
    public function __construct(
        public readonly array $products,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            products: data_get($data, 'products', []),
        );
    }
}
