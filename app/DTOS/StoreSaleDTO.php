<?php

namespace App\DTOS;

use App\Interfaces\DTOInterface;

class StoreSaleDTO implements DTOInterface
{
    public function __construct(
        public readonly string $payment_method,
        public readonly float $total,
        public readonly int $id_cashier,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            payment_method: data_get($data, 'payment_method'),
            total: data_get($data, 'total'),
            id_cashier: data_get($data, 'id_cashier'),
        );
    }
}
