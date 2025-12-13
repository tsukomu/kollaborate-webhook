<?php
/*
 * KOLLABORATE SPECIFIC FUNCTIONALITY
 */

// Check if $string is base64 encoded and decode it. Otherwise return original string.
// Thanks Kollaborate for randomly encoding some fields, sometimes
function decode($string): string
{
    if (!isset($string)) return "N/A";
    $start = substr($string, 0, 7);
    if (strcmp($start, "base64:") == 0) {
        $decoded = base64_decode(substr($string, 7));
        if ($decoded == false)
            return "";
        return $decoded;
    } else {
        return $string ?? "";
    }
}

function verifyKollaborateSignature($message, $signature)
{
    global $secret_key;

    // Split signature into algorithm and hash
    list($algorithm, $hash) = explode('=', $signature, 2);

    // Now calculate a hash based on the message and secret
    $new_hash = hash_hmac($algorithm, $message, $secret_key);

    // Check if calculated hash matches the provided signature
    return ($hash == $new_hash);
}

function sendResponseToServer($array)
{
    global $secret_key;
    file_log('WEBHOOK: SOLVING KOLLABORATE VERIFICATION CHALLENGE');

    // JSON encode
    $output = json_encode($array);

    // Sign the entire message
    $hash = hash_hmac('sha1', $output, $secret_key);

    header("X-Kollab-Signature: sha1=$hash");

    file_log('WEBHOOK: RESPONDING WITH VERIFICATION CHALLENGE RESULT');
    echo $output;
    flush();
}
