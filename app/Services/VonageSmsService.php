<?php

namespace App\Services;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class VonageSmsService
{
    protected Client $client;

    public function __construct()
    {
        $basic = new Basic(
            config('services.vonage.key'),
            config('services.vonage.secret')
        );

        $this->client = new Client($basic);
    }

    public function send(string $to, string $message)
    {
        return $this->client->sms()->send(
            new SMS(
                $to,
                config('services.vonage.sms_from'),
                $message
            )
        );
    }
}