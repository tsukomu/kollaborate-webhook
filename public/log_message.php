<?php

// Includes
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

function logWebhook($event)
{
    $ip = bold("IP: ");
    if (isset($event['ip_address']))
        $ip .= $event['ip_address'];
    else
        $ip .= "N/A";

    $decodedComment = mb_strimwidth(decode($event["comment"]), 0, 256, "...");
    $comment_meta = commentMeta($event);

    // We do a little conditional joining
    $comment_data = isset($event['timecode']) ? bold($event['timecode']) : "";
    if ($comment_meta)
        $comment_data .= n(1) . $comment_meta;
    if ($decodedComment)
        $comment_data .= n(1) . ">>> " . $decodedComment;

    $user = decode($event['user_name'] ?? "None");

    // The real (admin) link
    $vidlink = "https://www.kollaborate.tv/player?id=" . $event['file_id'];
    // The share link used, if any
    $linkUsed = "Link used: " . shareLink($event, "N/A");

    return [
        // The message itself - for logging
        // "content" => "",

        "embeds" => [
            [
                "timestamp" => now(),

                "color" => color($event),

                "thumbnail" => ["url" => thumbnail($event)],

                "description" => h(2, hyperlink(decode($event['title']), $vidlink)) . n(1) . h(3, $event['type']) . n(1) . $event['parent_path'] . n(1) . bold("Username: ") . $user . n(1) . $ip . n(1) . $linkUsed . n(2) . $comment_data,
            ]
        ]
    ];
}
