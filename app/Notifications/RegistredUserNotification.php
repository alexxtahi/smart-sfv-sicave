<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistredUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->success()
                    ->subject('Cration de votre compte SMART-SFV.')
                    ->from('tranxpert@smartyacademy.com', 'SMART-SFV')
                    ->line("Votre compte SMART-SFV a bien été crée mais il doit être confirmé. Merci de cliquer sur le bouton suivant.")
                    ->action('Confirmer mon compte', url("confirmer_compte/{$notifiable->id}/".urlencode($notifiable->confirmation_token)))
                    ->line('Ignonrer cet e-mail si ceci est une erreur.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
