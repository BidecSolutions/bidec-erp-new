<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Traits\PushNotification;
use Illuminate\Support\Facades\Log;

class SendLoginNotification
{
    use PushNotification;

    public function handle(UserLoggedIn $event)
    {

        $user = $event->user;

        if ($user->device_tokens) {
            $deviceTokens = json_decode($user->device_tokens, true);
            foreach ($deviceTokens as $deviceToken) {
                $this->sendNotification(
                    $deviceToken,
                    'Welcome Back!',
                    'You have successfully logged in.',
                    ['action' => 'login']
                );
            }
        }
    }
}
