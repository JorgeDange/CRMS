<?php

namespace App\Models;

use App\Enums\AlertType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Alert extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'entidade',
        'entidade_id',
        'mensagem',
        'user_id',
        'prazo',
        'lido',
        'resolvido',
        'resolvido_em',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => AlertType::class,
            'prazo' => 'date',
            'lido' => 'boolean',
            'resolvido' => 'boolean',
            'resolvido_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $alert): void {
            $alert->uuid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->lido = true;
        $this->save();
    }

    public function markAsResolved(): void
    {
        $this->resolvido = true;
        $this->resolvido_em = now();
        $this->save();
    }
}
