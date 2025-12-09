<?php

// Includes
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

function logWebhook($event)
{
    // The real (admin) link
    $vidlink = videoLink($event);
    // The share link used, if any
    $shareLink = shareLink($event, "N/A");

    $event_data = implode(n(1), array_filter(array(
        h(2, hyperlink(decode($event['title']), $vidlink)),
        bold("Parent path: ") . $event['parent_path'] ?? "N/A",
        bold("Event type: ") . $event['type'] ?? "null",
        bold("Share link: ") . $shareLink,
        bold("Username: ") . decode($event['user_name'] ?? "N/A"),
        bold("IP: ") . $event['ip'] ?? "N/A",
    )));

    switch ($event['type']) {
        case 'comment-new':
            return [
                "embeds" => [
                    [
                        "timestamp" => now(),
                        "color" => color($event),
                        "thumbnail" => ["url" => thumbnail($event)],
                        "description" => $event_data . n(2) . commentData($event),
                        "fields" => [
                            [
                                "name" => $event['timecode'],
                                "value" => "",
                                "inline" => false
                            ],
                        ]
                    ]

                ]
            ];
        case 'file-view':
        case 'file-upload':
            return [
                "embeds" => [
                    [
                        "timestamp" => now(),
                        "color" => color($event),
                        "thumbnail" => ["url" => thumbnail($event)],
                        "description" => $event_data,
                    ]
                ]
            ];
        default:
            if (isset($event['link_title'])) {
                $is_video = $event['file_type'] == 1;
                return [
                    "content" => "A new link has been created: " . hyperlink($event['link_title'], $shareLink),
                    "embeds" => [
                        "timestamp" => now(),
                        "color" => color($event),
                        "thumbnail" => ["url" => thumbnail($event)],
                        "description" => $event_data,
                    ]
                ];
            }
            return false;
    }
}
