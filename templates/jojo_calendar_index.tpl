{if $calendars}
{section name=c loop=$calendars}
<a href="{$prefix}/{$calendars[c].url}/">{$calendars[c].name}</a><br />
{/section}
{else}
<p>There are currently no calendars attached to this website.</p>
{/if}