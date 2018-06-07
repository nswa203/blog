<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\User;

class CustomResetPassword extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The callback that should be used to build the mail message.
     *
     * @var \Closure|null
     */
    public static $toMailCallback;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token, $newUser)
    {
        $this->token = $token;
        $this->newUser = $newUser;
        $this->host = env('APP_NAME');
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject('Welcome to ' . $this->host)
            ->greeting('Hello ' . $this->newUser->name . ',')
            ->line('You are receiving this eMail because one of our administrators has created a new account for you on ' . $this->host . ':')
            ->line('User:   ' . $this->newUser->email)
            ->line('Server: ' . url(config('app.url')))
            ->line('Please activate the account within 24 hours. Just use the button below and log in with a new password.')
            ->action('Activate Account',
                url(config('app.url') . route('password.reset',
                $this->token . '?email=' . $this->newUser->email,
                false)))
            ->line("If you're not interested in this account, no further action is required.");
    }

    /**
     * Set a callback that should be used when building the notification mail message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function toMailUsing($callback)
    {
        static::$toMailCallback = $callback;
    }
}
