<?php

// Includes
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

function publicWebhook($event)
{
    if (!isset($event['type'])) return false;
    if ($event['type'] != 'comment-new') return false;
    file_log("WEBHOOK: BUILDING PUBLIC MESSAGE", 2);

    global $link_list_components;
    return [
        "content" => commentContent($event),
        "embeds" => commentEmbeds($event),
        "components" => $link_list_components
    ];
}
