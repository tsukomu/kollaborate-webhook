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
            $id = $event['color_id'];
            if (!$id) {
                return 8421504; // gray for comments with no color set
            }
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

// This adds links to the message
function linkList($event)
{
    $sep = " **|** ";
    $kFolder = hyperlink("RICE Videos", "https://khat.us");
    $entries = hyperlink("Vid Info Sheet", "https://khat.us");
    $cws = hyperlink("CW & VPR Doc", "https://khat.us");
    $links = join($sep, [$kFolder, $entries, $cws]);
    return $links;
}

function commentData($event)
{
    return implode(n(1), array_filter(array(
        annotation($event),
        reply($event),
        bold(decode($event['user_name'])) . " wrote:",
        blockquote(commentText($event)),
    )));
}

function annotation($event)
{
    return  isset($event['has_annotation']) ? ":art: with an annotation" : null;
}

function commentMeta($event)
{
    return "";
}

function reply($event)
{
    return isset($event['reply_to_user_name']) ? ":arrow_right_hook: in reply to " . bold(decode($event['reply_to_user_name'])) : null;
}

function timecode($event)
{
    return isset($event['timecode']) ? bold($event['timecode']) : "";
}

function now()
{
    // Timestamp of embed must be formatted as ISO8601
    // without timestamp !!!
    // return preg_replace('/\+\d\d:\d\d$/i', "", date("c", strtotime("now")));
    return date("c", strtotime("now"));
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
    return "https://www.kollaborate.tv/player?id=" . $event['file_id'];
}

function thumbnail($event)
{
    return isset($event['icon']) ? "https://www.kollaborate.tv/" . $event['icon'] : "";
}


function commentText($event)
{
    return mb_strimwidth(decode($event["comment"] ?? ""), 0, 256, "...");
}
