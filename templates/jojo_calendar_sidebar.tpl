<div class="calendar_sidebar">
  <h2><a href="{$prefix}/{$calendar.url}/{$date.lastmonth|strtolower}-{$date.year}/" class="last_month">&lt;</a> {$date.month} {$date.year} <a href="{$prefix}/{$calendar.url}/{$date.nextmonth|strtolower}-{$date.year}/" class="next_month">&gt;</a></h2>
  
  {section name=d loop=$days}
  {if $days[d].weekday==$startofweek}<div class="week">{/if}
  
  <!-- [{$days[d].weekday} {$days[d].day} {$days[d].month}] -->
  <div class="{if $days[d].timestamp<$start || $days[d].timestamp>$end}outside{else}{$days[d].weekday}{/if}">
    
    {if $days[d].events}
    <a class="summary" href="{$prefix}/{$calendar.url}/{$days[d].day}-{$date.month|strtolower}-{$date.year}/">
    <h3><span>{$days[d].weekday} </span>{$days[d].day}<span> {$days[d].month} </span></h3>
    </a>
    
    <div class="full">
     <ul>
      {foreach from=$days[d].events item=event}
      <li>{$event.start|date_format:'%H:%M'} - {$event.title}</li>
      {/foreach}
    </ul>
    </div>
    {else}
    <h3><span>{$days[d].weekday} </span>{$days[d].day}<span> {$days[d].month} </span></h3>
    {/if}
  </div>
  {if $days[d].weekday==$endofweek}<div class="clear"></div>
  </div><!-- [end of week] -->{/if}
  {/section}

</div>