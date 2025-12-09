<?php

// Includes
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

function publicWebhook($event)
{
    if ($event['type'] != 'comment-new') return false;

    return [
        "embeds" => [
            [
                "timestamp" => now(),
                "color" => color($event),
                "thumbnail" => ["url" => thumbnail($event)],
                "title" => decode($event['title']),
                "url" => shareLink($event),
                "description" => commentData($event),
                "fields" => fields($event)
            ]
        ]
    ];
}

function fields($event)
{
    $fields = array();

    if (isset($event['timecode'])) {
        $fields[] = [
            "name" => $event['timecode'],
            "value" => $event['timecode'],
            "inline" => false
        ];
    }

    $fields[] = [
        "name" => " ",
        "value" => linkList($event),
        "inline" => false
    ];

    return $fields;
}
