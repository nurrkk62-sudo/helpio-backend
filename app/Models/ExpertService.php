<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpertService extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_id',
        'name',
        'description',
        'price',
        'duration_minutes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(Expert::class);
    }
}