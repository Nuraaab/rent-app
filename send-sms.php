<?php

require 'vendor/autoload.php';

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

$basic = new Basic(
    '8fac9aa3',
    'lIA!a657pp'
);

$client = new Client($basic);

$response = $client->sms()->send(
    new SMS(
        '+251925881509',
        'Vonage',
        'Hello from Vonage and Laravel'
    )
);

$message = $response->current();

echo "Message Status: " . $message->getStatus();