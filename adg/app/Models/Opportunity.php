<?php

namespace App\Models;

use App\Enums\OpportunityStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Opportunity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'nome',
        'empresa',
        'telefone',
        'email',
        'nif',
        'codigo_sap',
        'segmento',
        'localizacao',
        'origem',
        'interesse',
        'valor_estimado',
        'probabilidade',
        'data_fecho',
        'notas',
        'fase_atual',
        'motivo_nao_aprovacao',
        'data_aprovacao',
        'ultima_actividade_em',
        'ultimo_contacto_em',
    ];

    protected function casts(): array
    {
        return [
            'fase_atual' => OpportunityStage::class,
            'valor_estimado' => 'decimal:2',
            'probabilidade' => 'integer',
            'data_fecho' => 'date',
            'data_aprovacao' => 'datetime',
            'ultima_actividade_em' => 'datetime',
            'ultimo_contacto_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $opportunity): void {
            $opportunity->uuid ??= (string) Str::uuid();
        });
    }

    public function stageHistory(): HasMany
    {
        return $this->hasMany(OpportunityStageHistory::class);
    }

    public function visitReports(): HasMany
    {
        return $this->hasMany(VisitReport::class);
    }

    public function scopeStage(Builder $query, OpportunityStage $stage): Builder
    {
        return $query->where('fase_atual', $stage);
    }
}
