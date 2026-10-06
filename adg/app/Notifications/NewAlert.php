<?php

namespace App\Notifications;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Alert $alert
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tipoLabel = $this->alert->tipo->label();
        $entidadeLabel = match ($this->alert->entidade) {
            'opportunities' => 'Oportunidade',
            'contracts' => 'Contrato',
            'conciliactions' => 'Conciliação',
            default => 'Entidade',
        };

        return (new MailMessage)
            ->subject("ADG — Novo Alerta: {$tipoLabel}")
            ->greeting('Olá,')
            ->line("Foi gerado um novo alerta do tipo **{$tipoLabel}**.")
            ->line("**Entidade:** {$entidadeLabel}")
            ->line("**Mensagem:** {$this->alert->mensagem}")
            ->action('Ver no ADG', url('/alertas'))
            ->line('O sistema não move leads automaticamente — apenas notifica. A acção cabe a si.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'alert_id' => $this->alert->id,
            'tipo' => $this->alert->tipo->value,
            'mensagem' => $this->alert->mensagem,
        ];
    }
}
