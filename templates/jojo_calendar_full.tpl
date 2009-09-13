<div class="calendar_full">

  <h2><a href="{$prefix}/{$calendar.url}/{$date.lastmonth|strtolower}-{$date.year}/" class="last_month">&lt;</a> {$date.month} {$date.year} <a href="{$prefix}/{$calendar.url}/{$date.nextmonth|strtolower}-{$date.year}/" class="next_month">&gt;</a></h2>

  

  {section name=d loop=$days}

  {if $days[d].weekday==$startofweek}<div class="week">{/if}

  

  <!-- [{$days[d].weekday} {$days[d].day} {$days[d].month}] -->

  <div class="{if $days[d].timestamp<$start || $days[d].timestamp>$end}outside{else}{$days[d].weekday}{/if}">

    <h3><span>{$days[d].weekday} </span>{$days[d].day}<span> {$days[d].month} </span></h3>

    {if $days[d].events}

    <a class="summary" href="{$prefix}/{$calendar.url}/{$days[d].day}-{$days[d].month|strtolower}-{$date.year}/{if $offset!=0}GMT{$timezone}/{/if}">

    {foreach from=$days[d].events item=event}

    {if $event.start|date_format:'%M' == "00"}{$event.start|date_format:'%l %p'}{else}{$event.start|date_format:'%l:%M %p'}{/if} - {$event.title}<br />

    {/foreach}

    </a>

    

    <div class="full">

     <ul>

      {foreach from=$days[d].events item=event}

      <li>{if $event.start|date_format:'%M' == "00"}{$event.start|date_format:'%l %p'}{else}{$event.start|date_format:'%l:%M %p'}{/if} - {$event.title}</li>

      {/foreach}

    </ul>

    </div>

    

    {/if}

  </div>

  {if $days[d].weekday==$endofweek}<div class="clear"></div>

  </div><!-- [end of week] -->{/if}

  {/section}



{if $isplugin == true}

<form method="post" action="{$REQUEST_URI}">

<label><select name="timezone" class="timezone">

  <option value="">Set timezone</option>

  <option value="+13">GMT+13</option>

  <option value="+12">GMT+12</option>

  <option value="+11">GMT+11</option>

  <option value="+10">GMT+10</option>

  <option value="+9">GMT+9</option>

  <option value="+8">GMT+8</option>

  <option value="+7">GMT+7</option>

  <option value="+6">GMT+6</option>

  <option value="+5">GMT+5</option>

  <option value="+4">GMT+4</option>

  <option value="+3">GMT+3</option>

  <option value="+2">GMT+2</option>

  <option value="+1">GMT+1</option>

  <option value="">GMT</option>

  <option value="-1">GMT-1</option>

  <option value="-2">GMT-2</option>

  <option value="-3">GMT-3</option>

  <option value="-4">GMT-4</option>

  <option value="-5">GMT-5</option>

  <option value="-6">GMT-6</option>

  <option value="-7">GMT-7</option>

  <option value="-8">GMT-8</option>

  <option value="-9">GMT-9</option>

  <option value="-10">GMT-10</option>

  <option value="-11">GMT-11</option>

  <option value="-12">GMT-12</option>

  <option value="-13">GMT-13</option>

  </select></label>

  <input type="submit" name="submit" value="change" />

  </form>

{/if}

</div>