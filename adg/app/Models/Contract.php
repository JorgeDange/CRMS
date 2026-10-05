<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Contract extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'opportunity_id',
        'commercial_id',
        'product_id',
        'numero_contrato',
        'tipo',
        'data_inicio',
        'data_fim',
        'valor',
        'estado',
        'documento',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'estado' => ContractStatus::class,
            'valor' => 'decimal:2',
            'data_inicio' => 'date',
            'data_fim' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $contract): void {
            $contract->uuid ??= (string) Str::uuid();
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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
