<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'amount',
        'currency',
        'message',
        'eventTime',
        'isRead',
    ];

    protected $casts = [
        'amount' => 'float',
        'eventTime' => 'datetime',
        'isRead' => 'boolean',
    ];
}