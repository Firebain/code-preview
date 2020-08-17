<?php

namespace App\Notifications;

use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class VerifyEmail extends BaseVerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        $route = URL::route('verification.verify', ['id' => $notifiable->getKey()], false);
    
        return config('app.url') . $route;
    }
}
