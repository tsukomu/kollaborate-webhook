<?php

/*
 * DISCORD SPECIFIC FUNCTIONALITY
 */

// Sends the message object to Discords API
function sendDiscordMessage($messageData, $url, $withComponents = false)
{
    try {
        if ($withComponents) {
            $url .= "?with_components=true";
        }
        $curl = curl_init($url);

        $jsonData = json_encode($messageData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, TRUE);

        // curl debug logging
        // ob_start();
        // $out = fopen('php://output', 'w');        
        // curl_setopt($curl, CURLOPT_VERBOSE, true);
        // curl_setopt($curl, CURLOPT_STDERR, $out);

        file_log("WEBHOOK: SENDING DISCORD MESSAGE");
        file_log($jsonData, 2);
        $response = curl_exec($curl);

        // fclose($out);
        // $debug = ob_get_clean();
        // file_log($debug);
        file_log("WEBHOOK: DISCORD RESPONSE", 2);
        file_log($response, 2);
    } catch (Throwable $th) {
        file_log("WEBHOOK: sendDiscordMessage ERROR", 0);
        file_log($th);
    }
}
