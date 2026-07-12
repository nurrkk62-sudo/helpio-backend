<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * Relasi Expert dimiliki oleh User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi Expert dimiliki oleh Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}