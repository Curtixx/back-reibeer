<?php

namespace App\Models;

use App\CashierStatus;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    protected $fillable = [
        'initial_amount',
        'user_id_open',
        'user_id_close',
        'opened_at',
        'closed_at',
        'total_sales',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => CashierStatus::class,
        ];
    }

    public function userOpen()
    {
        return $this->belongsTo(User::class, 'user_id_open');
    }

    public function userClose()
    {
        return $this->belongsTo(User::class, 'user_id_close');
    }
}
