<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'opportunity_id',
        'user_id',
        'tipo',
        'descricao',
        'resultado',
        'data',
        'hora',
        'proxima_accao',
        'proxima_accao_prazo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => ActivityType::class,
            'status' => ActivityStatus::class,
            'data' => 'date',
            'proxima_accao_prazo' => 'date',
            // coluna TIME do esquema 8.4 — mantida como texto (H:i) para o
            // MySQL nao receber um datetime completo
            'hora' => 'string',
        ];
    }

    /**
     * Hora no formato curto do prototipo (08:00), pronta para apresentacao.
     */
    public function horaCurta(): ?string
    {
        if ($this->hora === null || $this->hora === '') {
            return null;
        }

        return substr($this->hora, 0, 5);
    }

    protected static function booted(): void
    {
        static::creating(function (self $activity): void {
            $activity->uuid ??= (string) Str::uuid();
        });
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ActivityStatus::PENDENTE);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->pending()
            ->whereNotNull('proxima_accao_prazo')
            ->where('proxima_accao_prazo', '<', now()->toDateString());
    }

    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->pending()
            ->whereNotNull('proxima_accao_prazo')
            ->whereBetween('proxima_accao_prazo', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }
}
