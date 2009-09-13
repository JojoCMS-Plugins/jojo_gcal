<?php

$href = Jojo::getFormData('href', false);

if ($href) {
    $href = preg_replace('%http://\\S*?/(.*)%i', '$1', $href);
    preg_match_all('%.*?/(.*?)/(january|february|march|april|may|june|july|august|september|october|november|december)-([0-9]+)/$%', $href, $result, PREG_PATTERN_ORDER);
    $name  = $result[1][0];
    $month = $result[2][0];
    $year  = $result[3][0];
    $start = strtotime('1 '.$month.' '.$year);
} else {
    $start = Jojo_Plugin_Jojo_gcal::getFirstDayOfMonth();
}

$events = Jojo_Plugin_Jojo_gcal::getEvents($name, $start);        
echo Jojo_Plugin_Jojo_gcal::getCalendarHtml($name, $events, $start);
exit;