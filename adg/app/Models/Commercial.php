<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Commercial extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $commercial): void {
            $commercial->uuid ??= (string) Str::uuid();
        });
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
