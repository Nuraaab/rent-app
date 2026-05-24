<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioSmsService {
    public function send(string $to, string $message): void {
        $client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        \Log::error('TwilioSmsService send method called');
        \Log::error($client);
        $client->messages->create($to, [
            'from' => config('services.twilio.from'),
            'body' => $message,
        ]);
    }
}
