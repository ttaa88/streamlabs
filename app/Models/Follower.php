<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'eventTime',
        'isRead',
    ];

    protected $casts = [
        'eventTime' => 'datetime',
        'isRead' => 'boolean',
    ];
}
