<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // 必須: Authenticatableを継承
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable // Authenticatableを継承
{
    use HasFactory, Notifiable;


    protected $fillable = [
        'name', 'email', 'password', 'birthday', 'authority','two-factor-attempts','two_factor_lockout_until',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_lockout_until' => 'datetime',
    ];
}
