<?php
/* !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! *
 * !!! Make a copy of this file as environment.php to override the settings !!! *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! */

// public-facing webhook
$public_webhook = "";
// private (admin only) webhook where logs are sent
$logging_webhook = "";
// secret key in Kollaborate webhook settings
$secret_key = "";

// Security settings
/* This variable controls checking the incoming requests for the
 * X-Kollaborate-Signature header, and verifying if it matches
 * your secret_key. Historically, Kollaborate has not bothered
 * to send the header in all requests, even though their docs say
 * they will always send it.
 */
$kollaborate_has_fixed_their_shit = false;

// General settings
$comment_max_length = 2048;

// Logging settings
/* If log_to_file = true, will log to the specified file, otherwise will log to the 
 * standard error_log. This should only be true when debugging, as it's insecure to
 * let people write literally anything on your server. When debugging, remember that
 * actually fatal errors will always be logged to the standard error_log.
 */
$log_to_file = false;
/* File where logs are written to when $log_to_file = true;
 * The file needs to be writeable by www-data (or the user that runs your web server)
 */
$log_file = "/tmp/webhook.log";
/* Logging level. Currently there are two levels ERROR (0) and INFO(1)
 * Set to 2 to log everything
 * Set to 1 to log information (and errors)
 * Set to 0 to only log errors
 * Set to -1 to log nothing
 */
$log_level = 0;

// Link list settings
/* This defines the content of the "components" field sent to Discord.
 * PHP automatically transforms [] into JSON {} OR [] when needed.
 * https://discord.com/developers/docs/components/reference
 * Webhooks only support basic components, you need to make an "app"
 * for Components V2. This project does not support Components V2.
 */
$link_list_components = [
    [
        "type" => 1,
        "components" => [
            [
                "type" => 2,
                "style" => 5,
                "label" => "Kollaborate videos folder",
                "emoji" => ["name" => "📂"],
                "url" => "https://kollaborate.tv/",
            ],
            [
                "type" => 2,
                "style" => 5,
                "label" => "Contest information",
                "emoji" => ["name" => "ℹ️"],
                "url" => "https://bentovid.com/",
            ],
            [
                "type" => 2,
                "style" => 5,
                "label" => "Kollaborate Webhook repository",
                "emoji" => ["name" => "⚠️"],
                "url" => "https://github.com/tsukomu/kollaborate-webhook",
            ],
        ]
    ]
];
