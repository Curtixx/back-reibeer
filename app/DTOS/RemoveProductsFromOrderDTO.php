<?php

namespace App\DTOS;

use App\Interfaces\DTOInterface;

class RemoveProductsFromOrderDTO implements DTOInterface
{
    public function __construct(
        public readonly array $product_ids,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_ids: data_get($data, 'product_ids', []),
        );
    }
}
