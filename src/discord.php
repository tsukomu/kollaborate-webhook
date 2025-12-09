<?php

function sendDiscordMessage($messageData, $url)
{
    try {
        $curl = curl_init($url);

        $jsonData = json_encode($messageData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
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

        $response = curl_exec($curl);

        // fclose($out);
        // $debug = ob_get_clean();
        // file_log($debug);
        file_log("WEBHOOK: DISCORD RESPONSE");
        file_log($response);
    } catch (Throwable $th) {
        file_log("WEBHOOK: sendDiscordMessage ERROR");
        file_log($th);
    }
}
