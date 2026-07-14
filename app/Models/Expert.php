<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expert extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara mass assignment.
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'location',
        'experience',
        'rating',
        'review_count',
        'completed_jobs',
        'starting_price',
        'banner',
        'bio',
        'operating_hours',
        'verified',
        'verification_status',
    ];

    /**
     * Casting atribut model.
     */
    protected function casts(): array
    {
        return [
            'rating' => 'decimal:1',
            'review_count' => 'integer',
            'completed_jobs' => 'integer',
            'starting_price' => 'decimal:2',
            'verified' => 'boolean',
        ];
    }

    /**
     * Relasi Expert dimiliki oleh User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    /**
     * Relasi Expert dimiliki oleh Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class
        );
    }

    /**
     * Relasi Expert memiliki banyak layanan.
     */
    public function services(): HasMany
    {
        return $this->hasMany(
            ExpertService::class
        );
    }

    /**
     * Relasi Expert memiliki banyak pesanan.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class
        );
    }

    /**
     * Relasi Expert memiliki banyak review.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class
        );
    }
}