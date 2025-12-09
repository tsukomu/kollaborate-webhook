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
    return "[" . $display . "](<" . $url . ">)";
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
