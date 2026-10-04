<?php

namespace App\Services;

use App\Enums\ActivityStatus;
use App\Models\Activity;
use App\Models\User;

class ActivityService
{
    /**
     * Cria uma actividade da Agenda (Sprint 5 / esquema 8.4).
     * O utilizador vem do auth (projecto de utilizador unico) e o estado
     * inicia sempre em "Pendente" (regra de apps-notes.html).
     */
    public function create(array $data): Activity
    {
        $data['user_id'] ??= auth()->id() ?? User::query()->value('id');
        $data['status'] ??= ActivityStatus::PENDENTE->value;

        return Activity::create($data);
    }

    /**
     * Actualiza os campos editaveis de uma actividade.
     */
    public function update(Activity $activity, array $data): Activity
    {
        $activity->fill($data);
        $activity->save();

        return $activity;
    }

    /**
     * Alterna Pendente <-> Concluida (checkbox e botao "Concluir" do
     * apps-tasks.html).
     */
    public function toggleStatus(Activity $activity): Activity
    {
        $activity->status = $activity->status === ActivityStatus::CONCLUIDA
            ? ActivityStatus::PENDENTE
            : ActivityStatus::CONCLUIDA;

        $activity->save();

        return $activity;
    }

    /**
     * Define o estado da actividade.
     */
    public function setStatus(Activity $activity, ActivityStatus $status): Activity
    {
        $activity->status = $status;
        $activity->save();

        return $activity;
    }

    /**
     * Elimina definitivamente (o esquema 8.4 nao tem soft deletes).
     */
    public function remove(Activity $activity): void
    {
        $activity->delete();
    }
}
