<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * Atribut yang boleh diisi secara mass assignment.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar',
        'address',
        'phone_verified_at',
    ];

    /**
     * Atribut yang disembunyikan dari response JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting atribut model.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi user dengan profil expert.
     */
    public function expert(): HasOne
    {
        return $this->hasOne(
            Expert::class
        );
    }

    /**
     * Relasi user dengan OTP verification.
     */
    public function otpVerifications(): HasMany
    {
        return $this->hasMany(
            OtpVerification::class
        );
    }

    /**
     * Relasi user dengan pesanan.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class
        );
    }

    /**
     * Relasi user dengan review.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class
        );
    }
}