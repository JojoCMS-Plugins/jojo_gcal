{section name=e loop=$events}
<h2>{if $events[e].start|date_format:'%M' == "00"}{$events[e].start|date_format:'%l %p'}{else}{$events[e].start|date_format:'%l:%M %p'}{/if} {$events[e].title}</h2>
{$events[e].body_html}

{/section}