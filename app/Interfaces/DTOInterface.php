<?php

namespace App\Interfaces;

interface DTOInterface
{
    public static function fromArray(array $data): self;
}
