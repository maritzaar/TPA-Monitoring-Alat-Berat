<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnomalyDetectedNotification extends Notification
{
    use Queueable;

    public $alatData;
    public $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($alatData, $message)
    {
        $this->alatData = $alatData;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id_aset' => $this->alatData->id_aset ?? ($this->alatData->unit_code ?? '-'),
            'tanggal' => $this->alatData->tanggal ?? null,
            'message' => $this->message,
            'persen_idle' => $this->alatData->persen_idle ?? null,
            'url' => route('monitoring.working_hour')
        ];
    }
}
