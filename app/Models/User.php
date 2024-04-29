<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'username',
        'email',
        'first_name',
        'last_name',
        'job_title',
        'phone',
        'birthdate',
        'cv',
        'profile_picture',
        'password',
        'role',
    ];
}
