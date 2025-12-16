<?php

// import the default and environment specific settings
include_once __DIR__ . "/../defaults.php";
include_once __DIR__ . "/../environment.php";
global $public_webhook, $logging_webhook, $secret_key, $link_list_components;

// Functions
include_once __DIR__ . "/../src/kollaborate.php";
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

// Webhook message formats
include_once __DIR__ . "/log_message.php";
include_once __DIR__ . "/public_message.php";

webhook_start();

// Check necessary variables are set
file_log("WEBHOOK: checking webhook URLs are set");
if (!isset($public_webhook)) {
    file_log("WEBHOOK: PUBLIC WEBHOOK URL IS MISSING", 0);
    exit(1);
}
if (!isset($logging_webhook)) {
    file_log("WEBHOOK: LOGGING WEBHOOK URL IS MISSING", 0);
    exit(1);
}
file_log("WEBHOOK: checking secret key is set");
if (!isset($secret_key)) {
    file_log("WEBHOOK: SECRET KEY IS MISSING", 0);
    exit(1);
}
file_log("WEBHOOK: checking link list components are defined properly");
if (!isset($link_list_components[0]['components'])) {
    file_log("WEBHOOK: \$link_list_components IS MISCONFIGURED", 0);
    exit(1);
}

// Check X-Kollaborate-Signature is valid
/* Anyone can send requests to the webhook, such is the 
 * nature of the internet. Only requests that have been
 * signed with this header can be trusted.
 */
if ($kollaborate_has_fixed_their_shit) {
    // Get request headers
    $headers = apache_request_headers();
    if (!$headers) {
        file_log("Error: couldn't read request headers. This should only happen if your webserver does not support the apache_request_headers php function", 0);
        exit(1);
    }
    // Check that the server issued a signature in the headers
    if (!isset($headers['X-Kollab-Signature'])) {
        file_log('Error: no signature received by server. Every message must have a signature.', 0);
        exit(1);
    }

    $signature = $headers['X-Kollab-Signature'];
    file_log('WEBHOOK: GOT SIGNATURE');

    // Verify the signature is correct
    if (!verifyKollaborateSignature($message, $signature)) {
        file_log('Error: message and signature received from server do not match. Check that your secret key is correct.', 0);
        exit;
    }
}

$message = file_get_contents('php://input');
file_log("WEBHOOK: RAW REQUEST" . $message, 2);

// Convert message to a JSON object
$json = (array) json_decode($message);
if (!$json) {
    file_log("Error: couldn't parse JSON. Payload may be corrupt or blank.", 0);
    exit(1);
}
file_log('WEBHOOK: PARSED JSON ' . print_r($json, true), 2);

// Handle Kollaborate webhook confirmation challenge
if (isset($json['challenge'])) {
    handleChallenge($json);
}

// Loop over the individual events inside the webhook request 
foreach ($json as $event) {
    $event = (array) $event;
    file_log('WEBHOOK: $event ' . print_r($event, true), 2);

    $publicMessageHasComponents = true;
    $publicMessage = publicWebhook($event);
    file_log($publicMessage, 2);
    if ($publicMessage) {
        sendDiscordMessage($publicMessage, $public_webhook, $publicMessageHasComponents);
    }

    $logMessageHasComponents = false;
    $privateMessage = logWebhook($event);
    file_log($privateMessage, 2);
    if ($privateMessage) {
        sendDiscordMessage($privateMessage, $logging_webhook, $logMessageHasComponents);
    }
}

webhook_end();
