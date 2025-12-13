<?php

/*
 * MARKDOWN FORMATTING FUNCTIONS
 */

function n($times = 1)
{
    return str_repeat("\n", $times);
}

function hyperlink($display, $url)
{
    if (!$url) return $display;
    return "[" . $display . "](<" . $url . ">)";
}

function markdownTimestamp($event)
{
    if (!isset($event['timestamp'])) return "N/A";
    return "<t:" . $event['timestamp'] . ":T>";
}

function bold($s)
{
    return "**" . $s . "**";
}

function h($level = 1, $string = "")
{
    $heading = str_repeat("#", $level);
    if ($string)
        $heading .= " " . $string;
    return $heading;
}

function blockquote($string = "")
{
    return ">>> " . $string;
}
