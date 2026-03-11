<?php

/*
 * SHARED $event (Kollaborate) to $message (Discord) UTILITIES
 */

// Transform Kollaborate color id to decimal color for Discord
function color($event)
{
    switch ($event['type']) {
        case 'file-upload':
            return 16711680; // bright red for file uploads
        case 'comment-new':
            if (!isset($event['color_id']) || !$event['color_id']) {
                return 8421504; // gray for comments with no color set
            }
            $id = $event['color_id'];
            $colors = [
                8421504,    // 0 - no color or grey
                8388608,    // 1 - red
                14671616,   // 2 - yellow
                16744192,   // 3 - orange
                40704,      // 4 - green
                255,        // 5 - blue
                3200456,    // 6 - turquoise
                16746653,   // 7 - pink
                8388736,    // 8 - purple
                16777215,   // 9 - white
                0           // 10 - black
            ];
            if (!isset($colors[$id]))
                return $colors[0];
            return $colors[$id];
        default:
            return 12945685; // gold for non-comments
    }
}

function commentText($event)
{
    global $comment_max_length;
    return mb_strimwidth(decode($event["comment"] ?? ""), 0, $comment_max_length, "...");
}

function commentContent($event)
{
    return implode(n(1), array_filter([
        h(2, hyperlink(videoTitle($event), shareLink($event, ""))),
        annotation($event),
    ]));
}

function commentEmbeds($event)
{
    // The actual timestamp when the comment was made on Kollaborate
    $timestamp = discordTimestamp($event);

    return [
        [
            "timestamp" => $timestamp,
            "color" => color($event),
            "author" => [
                "name" => "from " . username($event) . reply($event)
            ],
            "description" => [
                "text" => "**at " . timecode($event) . "**\n" . commentDescription($event),
            ]
        ]
    ];
}

function fileViewEmbeds($event)
{
    // The actual timestamp when the event happened on Kollaborate
    $timestamp = discordTimestamp($event);
    return [
        [
            "timestamp" => $timestamp,
            "author" => [
                "name" => username($event)
            ],
        ]
    ];
}

function username($event, $fallback = "")
{
    if (!isset($event['user_name'])) return $fallback;
    return decode($event['user_name']);
}

function videoTitle($event, $fallback = "")
{
    if (!isset($event['title'])) return $fallback;
    return decode($event['title']);
}

function eventType($event, $fallback = "")
{
    if (!isset($event['type'])) return $fallback;
    return decode($event['type']);
}

function parentPath($event, $fallback = "")
{
    if (!isset($event['parent_path'])) return $fallback;
    return decode($event['parent_path']);
}

function commentDescription($event)
{
    return commentText($event);
}

function annotation($event)
{
    return isset($event['has_annotation']) ? ":art: with an annotation" : null;
}

function reply($event)
{
    return isset($event['reply_to_user_name'])
        ? " in reply to " . decode($event['reply_to_user_name'])
        : null;
}

function timecode($event)
{
    return $event['timecode'] ?? "";
}

function discordTimestamp($event)
{
    if (isset($event['timestamp']))
        return date("c", $event['timestamp']);
    return "";
}

function now()
{
    return date("c", strtotime("now"));
}

function ip($event)
{
    if (isset($event['ip'])) return $event['ip'];
    if (isset($event['ip_address'])) return $event['ip_address'];
    return "N/A";
}

// Returns the public link to the video if available or the link used
function shareLink($event, string $fallback  = ""): string
{
    // If link_id isn't set, this is an admin using the real link
    if (!isset($event["link_id"]))
        return $fallback;
    if (isset($event["file_id"]))
        return "https://www.kollaborate.tv/player?id=" . $event["file_id"] . "&link=" . $event['link_id'];
    return "https://www.kollaborate.tv/link?id=" . $event['link_id'];
}

function videoLink($event)
{
    if (!isset($event['file_id'])) return "N/A";
    return "https://www.kollaborate.tv/player?id=" . $event['file_id'];
}

function thumbnail($event)
{
    return isset($event['icon']) ? "https://www.kollaborate.tv/" . $event['icon'] : "";
}
