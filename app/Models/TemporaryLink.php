<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'url',
        'user_id',
        'used',
    ];
}
