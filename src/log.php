<?php

/*
 * LOGGING FUNCTIONALITY
 */

function file_log($log_message)
{
    // Get settings from environment.php
    global $log_to_file, $log_file;

    // Ensure message is a string
    if (!is_string($log_message)) {
        $log_message = print_r($log_message, true);
    }

    // Manually write to file to get around maximum length limitation
    if ($log_to_file) {
        $log_message .= "\n";
        file_put_contents($log_file, $log_message, FILE_APPEND);
    } else {
        error_log($log_message);
    }
}
