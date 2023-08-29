<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'itemName',
        'amount',
        'price',
        'eventTime',
        'isRead',
    ];

    protected $casts = [
        'amount' => 'integer',
        'price' => 'float',
        'eventTime' => 'datetime',
        'isRead' => 'boolean',
    ];
}
