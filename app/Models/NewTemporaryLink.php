<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewTemporaryLink extends Model
{
    use HasFactory;

    protected $table = 'new_temporary_links';

    protected $fillable = [
        'url',
        'user_id',
        'ip_address',
        'user_agent',
        'used',
    ];
}

