<?php

// If true, will log to the specified file, otherwise will log to standard error_log
$log_to_file = true;
$log_file = "/tmp/webhook.log";

// Enhanced logging
function file_log($log_message)
{
    global $log_to_file, $log_file;
    if ($log_to_file) {
        file_put_contents($log_file, $log_message, FILE_APPEND);
    } else {
        error_log($log_message);
    }
}
