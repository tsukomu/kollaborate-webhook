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

    // Build the log data
    $event_data = implode(n(1), array_filter([
        h(2, hyperlink(decode($event['title']), $vidlink)),
        bold("Event type: ") . (isset($event['type']) ? $event['type'] : "null"),
        bold("Parent path: ") . (isset($event['parent_path']) ? $event['parent_path'] : "N/A"),
        bold("Share link: ") . $shareLink,
        bold("Username: ") . decode($event['user_name']),
        bold("IP: ") . ip($event),
    ]));

    switch ($event['type']) {
        case 'comment-new':
            file_log("WEBHOOK: LOGGING comment-new EVENT");
            return [
                "content" => implode(
                    n(1),
                    array_filter([
                        $event_data,
                        reply($event),
                        annotation($event),
                    ])
                ),
                "embeds" => commentEmbeds($event),
            ];
        case 'file-view':
        case 'file-upload':
            file_log("WEBHOOK: LOGGING file-view OR file-upload EVENT");
            return [
                "content" => $event_data,
                "embeds" => fileViewEmbeds($event),
            ];
        default:
            file_log("WEBHOOK: LOGGING null EVENT");
            if (isset($event['link_title'])) {
                file_log("WEBHOOK: LOGGING new link creation");
                $linkContent = "A new link has been created: "
                    . hyperlink(isset($event['link_title']) ? $event['link_title'] : "NO LINK TITLE", $shareLink);
                // $is_video = $event['file_type'] == 1;
                return [
                    "content" => $linkContent . n(1) . $event_data,
                    "embeds" => fileViewEmbeds($event),
                ];
            }
            return false;
    }
}
