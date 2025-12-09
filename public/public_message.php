<?php

// Includes
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

function publicWebhook($event)
{
    $decodedComment = mb_strimwidth(decode($event["comment"]), 0, 256, "...");
    $comment_meta = commentMeta($event);

    return [
        // The message itself - public version
        // "content" => "",

        "embeds" => [
            [
                "timestamp" => now(),

                "color" => color($event),

                "thumbnail" => ["url" => thumbnail($event)],

                "title" => decode($event['title']),

                "url" => shareLink($event),

                "description" => bold(decode($event['user_name'])) . " wrote:" . n(1) . ">>> " . $decodedComment,

                "fields" => [
                    [
                        "name" => $event['timecode'],
                        "value" => $comment_meta,
                        "inline" => false
                    ],
                    [
                        "name" => " ",
                        "value" => linkList($event),
                        "inline" => false
                    ]
                ]
            ]
        ]
    ];
}
