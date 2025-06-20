<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;

class CustomResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    /**
     * Constructor de la notificación.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Define los canales de notificación.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Construye el mensaje del correo.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recuperación de Contraseña - Sistema de Resguardos de Inventarios')
            ->view('emails.reset-password', [
                'token' => $this->token,
                'email' => $notifiable->email
            ]);
    }
}
