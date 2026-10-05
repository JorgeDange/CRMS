<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'numero_serie',
        'codigo',
        'valor',
        'commercial_id',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'valor' => 'decimal:2',
            'activo' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $product): void {
            $product->uuid ??= (string) Str::uuid();
        });
    }

    public function commercial(): BelongsTo
    {
        return $this->belongsTo(Commercial::class);
    }

    public function opportunities(): BelongsToMany
    {
        return $this->belongsToMany(Opportunity::class, 'opportunity_product')
            ->withTimestamps();
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
