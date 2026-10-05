<?php

namespace App\Models;

use App\Enums\ConciliactionClassificacao;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Conciliaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'commercial_id',
        'data',
        'valor',
        'classificacao',
        'observacoes',
        'resolvido',
        'resolvido_em',
    ];

    protected function casts(): array
    {
        return [
            'classificacao' => ConciliactionClassificacao::class,
            'valor' => 'decimal:2',
            'data' => 'date',
            'resolvido' => 'boolean',
            'resolvido_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $conciliaction): void {
            $conciliaction->uuid ??= (string) Str::uuid();
        });
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function commercial(): BelongsTo
    {
        return $this->belongsTo(Commercial::class);
    }
}
