<?php

// Includes
include_once __DIR__ . "/../src/markdown.php";
include_once __DIR__ . "/../src/discord.php";
include_once __DIR__ . "/../src/log.php";
include_once __DIR__ . "/../src/message.php";

function logWebhook($event)
{
    file_log("WEBHOOK: BUILDING LOG MESSAGE");
    // The real (admin) link
    $vidlink = videoLink($event);
    // The share link used, if any
    $shareLink = shareLink($event, "N/A");
    // The actual timestamp from Kollab
    $timestamp = discordTimestamp($event['timestamp']);

    $event_data = implode(n(1), array_filter(array(
        h(2, hyperlink(decode($event['title']), $vidlink)),
        bold("Parent path: ") . isset($event['parent_path']) ? $event['parent_path'] : "N/A",
        bold("Event type: ") . isset($event['type']) ? $event['type'] : "null",
        bold("Share link: ") . $shareLink,
        bold("Username: ") . decode($event['user_name']),
        bold("IP: ") . isset($event['ip']) ? $event['ip'] : "N/A",
    )));

    switch ($event['type']) {
        case 'comment-new':
            file_log("WEBHOOK: LOGGING comment-new EVENT");
            return [
                "embeds" => [
                    [
                        "timestamp" => $timestamp,
                        "color" => color($event),
                        "thumbnail" => ["url" => thumbnail($event)],
                        "description" => $event_data . n(2) . commentData($event),
                        "fields" => [
                            [
                                "name" => isset($event['timecode']) ? $event['timecode'] : "no timecode??",
                                "value" => "",
                                "inline" => false
                            ],
                        ]
                    ]

                ]
            ];
        case 'file-view':
        case 'file-upload':
            file_log("WEBHOOK: LOGGING file-view OR file-upload EVENT");
            return [
                "embeds" => [
                    [
                        "timestamp" => $timestamp,
                        "color" => color($event),
                        "thumbnail" => ["url" => thumbnail($event)],
                        "description" => $event_data,
                    ]
                ]
            ];
        default:
            file_log("WEBHOOK: LOGGING null EVENT");
            if (isset($event['link_title'])) {
                file_log("WEBHOOK: LOGGING new link creation");
                // $is_video = $event['file_type'] == 1;
                return [
                    "content" => "A new link has been created: "
                        . hyperlink(isset($event['link_title']) ? $event['link_title'] : "no link?", $shareLink),
                    "embeds" => [
                        [
                            "timestamp" => $timestamp,
                            "thumbnail" => ["url" => thumbnail($event)],
                            "description" => $event_data,
                        ]
                    ]
                ];
            }
            return false;
    }
}
