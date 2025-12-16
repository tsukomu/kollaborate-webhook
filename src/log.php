<?php

/*
 * LOGGING FUNCTIONALITY
 */

function file_log($log_message, $level = 1)
{
    // Get settings from environment.php
    global $log_to_file, $log_file, $log_level;

    if ($log_level < $level) return;

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

function webhook_start()
{
    file_log('WEBHOOK: START '  . date(DATE_RFC2822));
}

function webhook_end(): never
{
    file_log('WEBHOOK: SUCCESS '  . date(DATE_RFC2822));
    echo "OK";
    flush();
    exit;
}
