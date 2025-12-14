<?php

// import the secret variables
include __DIR__ . "/../environment.php";
global $public_webhook, $logging_webhook, $secret_key;

// Functions
include_once __DIR__ . "/../src/kollaborate.php";
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

// Webhook message formats
include_once __DIR__ . "/log_message.php";
include_once __DIR__ . "/public_message.php";

file_log('WEBHOOK: I AM RUNNING ' . date(DATE_RFC2822));

$message = file_get_contents('php://input');
file_log('WEBHOOK: $MESSAGE = \'' . $message . '\'');

// Check that the server issued a signature in the headers
// Kollab frequently doesn't send the signature so comment out security if necessary.

// $headers = getallheaders();
// $signature = $headers['X-Kollab-Signature'] ?? false;
// if (!$signature) {
//     file_log('Error: no signature received by server. Every message must have a signature.', 0);
//     exit;
// } else {
//     file_log('WEBHOOK: GOT SIGNATURE');
// }

// Verify the signature is correct
// if (!verifyKollaborateSignature($message, $signature)) {
//     file_log('Error: message and signature received from server do not match. Check that your secret key is correct.', 0);
//     exit;
// }

// Convert message to a JSON object
$json = (array) json_decode($message);
if (!$json) {
    file_log('Error creating JSON object. Payload may be corrupt or blank.', 0);
    exit;
}
file_log('WEBHOOK: PARSED JSON ' . print_r($json, true));

if (isset($json['challenge'])) {
    file_log('WEBHOOK: KOLLABORATE VERIFICATION CHALLENGE RECEIVED');
    $response = intval($json['challenge']) + 1;
    sendResponseToServer(['response' => $response]);
    file_log('WEBHOOK: END');
    exit;
} else {
    foreach ($json as $event) {
        $event = (array) $event;
        // uncomment next line to output each event to the error log because there are no docs
        // file_log('WEBHOOK: $event ' . print_r($event, true));
        $publicMessage = publicWebhook($event);
        file_log($publicMessage);
        if ($publicMessage)
            sendDiscordMessage($publicMessage, $public_webhook, true);
        $privateMessage = logWebhook($event);
        file_log($privateMessage);
        if ($privateMessage)
            sendDiscordMessage($privateMessage, $logging_webhook);
    }
    file_log('WEBHOOK: END');
    exit;
}
