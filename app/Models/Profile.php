<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'short_description',
        'logo',
        'favicon',
        'email',
        'phone_number',
        'address',
    ];
}
