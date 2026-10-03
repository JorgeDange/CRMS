<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpportunityStageHistory extends Model
{
    public const UPDATED_AT = null;

    /**
     * Nome da tabela conforme PROMPT_MASTER 8.3 (nao segue a pluralizacao padrao).
     */
    protected $table = 'opportunity_stage_history';

    protected $fillable = [
        'opportunity_id',
        'fase_anterior',
        'fase_nova',
        'user_id',
        'observacao',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
