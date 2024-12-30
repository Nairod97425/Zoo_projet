<?php
// src/Service/FirebaseService.php

namespace App\Service;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Messaging;

class FirebaseService
{
    private $firebase;

    public function __construct(string $firebaseCredentials)
    {
        $serviceAccount = ServiceAccount::fromJsonFile($firebaseCredentials);
        $this->firebase = (new Factory)->withServiceAccount($serviceAccount)->createMessaging();
    }

    public function sendPushNotification(string $deviceToken, string $title, string $body)
    {
        $message = Messaging\CloudMessage::new()
            ->withNotification(Messaging\Notification::create($title, $body))
            ->withData(['key' => 'value'])
            ->withToken($deviceToken);

        try {
            $this->firebase->send($message);
            return true;
        } catch (\Exception $e) {
            // Handle error appropriately
            return false;
        }
    }
}
