<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class OwnerVerifyEmail extends VerifyEmail
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Verifikasi Email - Kost Zone')
            ->greeting('Halo!')
            ->line('Terima kasih telah mendaftar di Kost Zone.')
            ->line('Silakan klik tombol di bawah untuk verifikasi email Anda.')
            ->action('Verifikasi Email', $url)
            ->line('Link verifikasi ini akan kadaluarsa dalam 60 menit.')
            ->line('Jika Anda tidak membuat akun, abaikan email ini.')
            ->salutation('Salam, Tim Kost Zone');
    }
}