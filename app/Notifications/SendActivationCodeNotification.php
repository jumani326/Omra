<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendActivationCodeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $activationCode
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('register.activate', [
            'token' => $this->activationCode,
            'email' => $notifiable->email,
        ]);

        return (new MailMessage)
            ->subject('Activez votre compte - Umrah Management System')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Vous vous êtes inscrit sur la plateforme Omra. Cliquez sur le bouton ci-dessous pour activer votre compte (lien valide 24 heures).')
            ->action('Activer mon compte', $url)
            ->line('Si vous n\'êtes pas à l\'origine de cette inscription, ignorez cet email.');
    }
}
