<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'expert_id',
        'service_title',
        'price',
        'address',
        'date',
        'time',
        'description',
        'photo_url',
        'notes',
        'status',
        'payment_method',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'date' => 'date:Y-m-d',
        ];
    }

    /**
     * Relasi order dengan user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    /**
     * Relasi order dengan expert.
     */
    public function expert(): BelongsTo
    {
        return $this->belongsTo(
            Expert::class
        );
    }

    /**
     * Satu order hanya memiliki satu review.
     */
    public function review(): HasOne
    {
        return $this->hasOne(
            Review::class
        );
    }
}