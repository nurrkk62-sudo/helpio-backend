<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expert extends Model
{
    use HasFactory;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class
        );
    }

    public function services(): HasMany
    {
        return $this->hasMany(
            ExpertService::class
        );
    }

    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class
        );
    }

    public function verification(): HasOne
    {
        return $this->hasOne(
            ExpertVerification::class
        );
    }
}