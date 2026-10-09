<?php

namespace App\Models;

use App\Enums\ConciliacaoEstado;
use App\Enums\ContratoEstado;
use App\Enums\OpportunityStage;
use App\Enums\TipoRelacao;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'pessoa_contacto',
        'empresa',
        'telefone',
        'email',
        'nif',
        'codigo_sap',
        'codigo',
        'tem_compras',
        'assinatura_director',
        'enviado_entregue',
        'conciliacao_estado',
        'contrato_estado',
        'sector',
        'localizacao',
        'origem',
        'tipo_relacao',
        'interesse',
        'valor_estimado',
        'probabilidade',
        'data_fecho',
        'comercial_id',
        'produto_id',
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
            'tipo_relacao' => TipoRelacao::class,
            'tem_compras' => 'boolean',
            'assinatura_director' => 'boolean',
            'enviado_entregue' => 'boolean',
            'conciliacao_estado' => ConciliacaoEstado::class,
            'contrato_estado' => ContratoEstado::class,
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

    public function commercial(): BelongsTo
    {
        return $this->belongsTo(Commercial::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'opportunity_product')
            ->withTimestamps();
    }

    public function scopeStage(Builder $query, OpportunityStage $stage): Builder
    {
        return $query->where('fase_atual', $stage);
    }
}
