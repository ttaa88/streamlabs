<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'tier',
        'eventTime',
        'isRead',
    ];

    protected $casts = [
        'tier' => 'integer',
        'eventTime' => 'datetime',
        'isRead' => 'boolean',
    ];
}
