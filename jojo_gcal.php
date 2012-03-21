<?php
/**
 * Jojo CMS - jojo_gcal - Google calendar integration
 *
 * Copyright 2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package empty_plugin
 */

class Jojo_Plugin_Jojo_gcal extends Jojo_Plugin
{
    function _getContent()
    {
        global $smarty;
        $content = array();
        
        $timezone     = Jojo::getPost('timezone', false);
        
        if ($timezone) {
            /* set timezone in cookie */
            
            /* change URI to reflect timezone */
            Jojo::redirect(_SITEURL.''.preg_replace('%(.*?)/GMT[+-]?[0-9]*/?$%', '$1', $_SERVER['REQUEST_URI']).'GMT'.$timezone.'/');
        }
        
        $smarty->assign('prefix', Jojo_Plugin_Jojo_gcal::urlPrefix());
        
        $calendarname = Jojo::getFormData('name', false);
        $day          = Jojo::getFormData('day', false);
        $month        = Jojo::getFormData('month', false);
        $year         = Jojo::getFormData('year', false);
        $timezone     = Jojo::getFormData('timezone', false);
        if ($calendarname !== false) {
            $isplugin = true;
            $smarty->assign('isplugin', $isplugin);
        }
        
        $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendarname); //retrieve authentication details from database
        if ($timezone === false) {
            $timezone = Jojo_Plugin_Jojo_gcal::getCalendartimezone($calendarname, Jojo_Plugin_Jojo_gcal::authenticate($calendar['user'], $calendar['password']));
            
        
            $timezone = Jojo_Plugin_Jojo_gcal::getTimezoneint($timezone);
            
           } 
        $smarty->assign('timezone', $timezone);
        

        if ($calendarname && !$day) {
            /* display monthly listings */
            if ($month && $year) {
                $events = Jojo_Plugin_Jojo_gcal::getEvents($calendarname, strtotime('1 '.$month.' '.$year), false, $timezone);
            } else {
                $events = Jojo_Plugin_Jojo_gcal::getEvents($calendarname, false, false, $timezone);
            }
            $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendarname);
            
            
            /* Add breadcrumb */
            $breadcrumbs = $this->_getBreadCrumbs();
            $breadcrumb = array();
            $breadcrumb['name']               = $calendar['name'];
            $breadcrumb['rollover']           = $calendar['name'];
            $breadcrumb['url']                = Jojo_Plugin_Jojo_gcal::urlPrefix().'/'.$calendar['url'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            

            $content['breadcrumbs']     = $breadcrumbs;
            $content['title']           = $calendar['name'];
            $content['seotitle']        = Jojo::either($calendar['seotitle'], $calendar['name']);
            $content['metadescription'] = !empty($calendar['metadescription']) ? $calendar['metadescription'] : '';
            $content['javascript']      = $smarty->fetch('jojo_calendar_full_js.tpl');
            $content['content']         = '<div id="calendar">'.Jojo_Plugin_Jojo_gcal::getCalendarHtml($calendarname, $events).'</div>';
            return $content;
        } elseif ($calendarname && $day) {
            /* event listing for a given day */
            
            $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendarname);
            $start = strtotime($day.' '.$month.' '.$year);
            //print_r($start.'--');
            $end = strtotime('+1 day'.$day.' '.$month.' '.$year);
            //print_r($end);
            $events = Jojo_Plugin_Jojo_gcal::getEvents($calendarname, $start, $end, $timezone);
            $n = count($events);
            for ($i=0; $i<$n; $i++) {
                $events[$i]['body_html'] = Jojo::markdown2html($events[$i]['body']);
            }
            $smarty->assign('events', $events);
            
            /* Add breadcrumb */
            $breadcrumbs = $this->_getBreadCrumbs();
            $breadcrumb = array();
            $breadcrumb['name']               = $calendar['name'];
            $breadcrumb['rollover']           = $calendar['name'];
            $breadcrumb['url']                = Jojo_Plugin_Jojo_gcal::urlPrefix().'/'.$calendar['url'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            
            $breadcrumb = array();
            $breadcrumb['name']               = date('j M Y', $start);
            $breadcrumb['rollover']           = date('l, jS F, Y', $start);
            $breadcrumb['url']                = Jojo_Plugin_Jojo_gcal::urlPrefix().'/'.$calendar['url'];
            $breadcrumbs[count($breadcrumbs)] = $breadcrumb;
            
            
            
            $content['title']       = $calendar['name'].' '.date('jS F', $start);
            $content['seotitle']    = $calendar['name'].' '.date('l, jS F, Y', $start);
            $content['breadcrumbs'] = $breadcrumbs;
            $content['content']     = $smarty->fetch('jojo_calendar_day.tpl');
            return $content;
        } else {
            /* calendar index page */
            $calendars = Jojo::selectQuery("SELECT * FROM {gcal} ORDER BY displayorder");
            $smarty->assign('calendars', $calendars);
            $content['content']  = $smarty->fetch('jojo_calendar_index.tpl');
            return $content;
        }
    }
    
    function urlPrefix()
    {
        return 'calendars';
    }
    
    function getCalendarData($input)
    {
        /* todo: cache results */
        $calendar = Jojo::selectRow("SELECT * FROM {gcal} WHERE url=? OR name=?", array($input, $input));
        if (!$calendar['calendarid']) return false;
        return $calendar;
    }

    function getEvents($calendar, $start=false, $end=false, $timezone=false)
    {
        
        //set timezone if false
        
        if ($timezone === false) {
            $calendardetails = Jojo_Plugin_Jojo_gcal::getCalendarData($calendar); //retrieve authentication details from database
            $timezone = Jojo_Plugin_Jojo_gcal::getCalendartimezone($calendar, Jojo_Plugin_Jojo_gcal::authenticate($calendardetails['user'], $calendardetails['password']));
            $timezone =Jojo_Plugin_Jojo_gcal::getTimezoneInt($timezone);
        }
        
        //set the calendar timezone
        $calendarinfo = Jojo_Plugin_Jojo_gcal::getCalendarData($calendar); //retrieve authentication details from database
        $calendartimezone = Jojo_Plugin_Jojo_gcal::getCalendartimezone($calendar, Jojo_Plugin_Jojo_gcal::authenticate($calendarinfo['user'], $calendarinfo['password']));
        $offset = Jojo_Plugin_Jojo_gcal::getOffset($calendartimezone, $timezone);
        $offset = $offset * 60 * 60;
        //print_r($offset);        
        $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendar); //retrieve authentication details from database
        if (!$calendar) return false;
        //print_r($start.'--'.$end);
        if (!$start) $start = Jojo_Plugin_Jojo_gcal::getFirstDayOfMonth();
        if (!$end)   $end   = Jojo_Plugin_Jojo_gcal::getLastDayOfMonth($start);
        
        $client = Jojo_Plugin_Jojo_gcal::authenticate($calendar['user'], $calendar['password'], $service);
        
        $events = Jojo_Plugin_Jojo_gcal::getCalendar($client, $calendar['googleid'], $timezone, $start, $end);
        //print_r($events);
        return $events;
    }
    
    function getCalendarHtml($calendar, $events, $start=false)
    {
        global $smarty;
        /* start / end defaults to current month */
        if (!$start) $start = Jojo_Plugin_Jojo_gcal::getFirstDayOfMonth();
        if (!$end)   $end   = Jojo_Plugin_Jojo_gcal::getLastDayOfMonth($start);
        
        $date = array();
        $date['month']     = date('F', $start);
        $date['year']      = date('Y', $start);
        $date['nextmonth'] = date('F', strtotime('+1 month', $start));
        $date['lastmonth'] = date('F', strtotime('-1 month', $start));
        $date['nextyear']  = date('Y', strtotime('+1 month', $start));
        $date['lastyear']  = date('Y', strtotime('-1 month', $start));
        $smarty->assign('date', $date);
        
        $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendar);

        $smarty->assign('calendar', $calendar);
        
        $startofweek = Jojo::getOption('calendar_start_of_week', 'mon');
        $endofweek   = strtolower(date('D', strtotime('+6 day', strtotime('this '.$startofweek.' 12:00'))));
        $actualstart = strtotime('last '.$startofweek, $start);
        $actualend   = strtotime('next '.$startofweek, $end);
        
        /* make days list */
        $days = array();
        
        $t = $actualstart;
        while ($t < $actualend) {
            $tPlusDay = strtotime('+1 day', $t);
            $e = array();
            foreach ($events as $event) {
                if (($event['start'] > $t) && ($event['start'] < $tPlusDay)) {$e[] = $event;}
            }
            $days[] = array('timestamp' => $t, 'day' => date('j', $t), 'weekday' => strtolower(date('D', $t)), 'month' => date('F', $t), 'events' => $e);
            $t = $tPlusDay;
        }

        $smarty->assign('startofweek', $startofweek);
        $smarty->assign('endofweek',   $endofweek);
        $smarty->assign('start',       $start);
        $smarty->assign('end',         $end);
        $smarty->assign('days',        $days);
        $smarty->assign('prefix',      Jojo_Plugin_Jojo_gcal::urlPrefix());
        
        $template = !empty($calendar['template']) ? $calendar['template'] : 'jojo_calendar_full.tpl';
        return $smarty->fetch($template);
    }
    
    function getCalendarList($client) 
    {
        $gdataCal = new Zend_Gdata_Calendar($client);
        $calFeed = $gdataCal->getCalendarListFeed();
        $title = $calFeed->title->text;
        $calendars = array();
        foreach ($calFeed as $calendar) {
            $calendars[] = $calendar->title->text;
        }
        return $calendars;
    }
    
    function getCalendar($client, $calendarid='default', $timezone=false, $startDate=false, $endDate=false) 
    {
        
        $gdataCal = new Zend_Gdata_Calendar($client);
        $query = $gdataCal->newEventQuery();
        $query->setUser($calendarid);
        $query->setVisibility('private');
        $query->setProjection('full');
        $query->setOrderby('starttime');
        $query->setsingleevents('true');
        $query->setSortOrder('ascending'); // either ascending or descending
        if (!startDate) {
          $query->setStartMin(self::getstartofweek($startDate));
        } else {
          $query->setStartMin($startDate);
        }
        if (!endDate) {
          $query->setStartMax(self::getendofweek($endDate));
        } else {
          $query->setStartMax($endDate);
        }

        $eventFeed = $gdataCal->getCalendarEventFeed($query);
        
        $calendartimezone = $eventFeed->timezone->value;
        
        $offset = Jojo_Plugin_Jojo_gcal::getOffset($calendartimezone, $timezone);
        
        
        $events = array();
        foreach ($eventFeed as $event) {
            $thisevent = array();          
            
            $thisevent['id'] = $event->id->text;
            $thisevent['title'] = $event->title->text;
            $thisevent['body'] = $event->content->text;
            $thisevent['when'] = array();
            foreach ($event->who as $who) {
                $thisevent['calendar'] = $who->valueString;
            }
            foreach ($event->when as $when) {
                $thisevent['when'][] = $when->startTime;
                $thisevent['start'] = strtotime($when->startTime);
                $thisevent['end'] = strtotime($when->endTime);
                //echo 't='.$timezone;
                //exit;
                /* make timezone adjustments */
                if ($offset !== false) {
                    $thisevent['start'] = $thisevent['start'] + ($offset * 60 * 60);
                    $thisevent['end']   = $thisevent['end']   + ($offset * 60 * 60);
                }
            }

            $events[] = $thisevent;
        }
        return $events;
    }
    
    function contentFilter($content)
    {
        preg_match_all('/\[\[gcal: ?([^\]]*)\]\]/', $content, $matches);
        foreach($matches[1] as $id => $name) {
            $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($name);
            if ($calendar) {
                $events  = Jojo_Plugin_Jojo_gcal::getEvents($name);
                $html    = '<div id="calendar">'.Jojo_Plugin_Jojo_gcal::getCalendarHtml($name, $events).'</div>';
                $content = str_replace($matches[0][$id], $html, $content);
            }
        }
        return $content;
    }
    
    function getFirstDayOfMonth($timestamp=false)
    {
        if (!$timestamp) $timestamp = time();
        $month = date('M', $timestamp);
        $month2 = date('M', strtotime('-1 day', $timestamp));
        $year = date('Y', $timestamp);
        return strToTime('1 '.$month.' '.$year);
    }
    
    function getLastDayOfMonth($timestamp=false)
    {
        if (!$timestamp) $timestamp = time();
        $lastday = date('t', $timestamp);
        $month = date('M', $timestamp);
        $year = date('Y', $timestamp);
        return strToTime('+23 hours 59 minutes 59 seconds'.$lastday.' '.$month.' '.$year);
    }
    
    function getCorrectUrl()
    {
        //Assume the URL is correct
        return _PROTOCOL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
    
    function getOffset($calendartimezone, $timezone)
    {
        global $smarty;
        
        $datetimezone_calendar = new DateTimeZone($calendartimezone);
        $datetime_calendar = new DateTime("now", $datetimezone_calendar);
        $offset_calendar = timezone_offset_get($datetimezone_calendar, $datetime_calendar);
        $offset_calendar = $offset_calendar / 60 / 60;
        $calendartimezoneint = $offset_calendar;
        
      /*  $datetimezone_calendar = new DateTimeZone($timezone);
        $datetime_calendar = new DateTime("now", $datetimezone_calendar);
        $offset_calendar = timezone_offset_get($datetimezone_calendar, $datetime_calendar);
        $offset_calendar = $offset_calendar / 60 / 60;
        $timezoneint = $offset_calendar; */
        
        $offset = $timezone - $calendartimezoneint;
        $smarty->assign('offset', $offset);
        return $offset;

    }
    
    function getTimezoneint($timezone)
    {
        $datetimezone_calendar = new DateTimeZone($timezone);
        $datetime_calendar = new DateTime("now", $datetimezone_calendar);
        $timezoneint = timezone_offset_get($datetimezone_calendar, $datetime_calendar);
        $timezoneint = $timezoneint / 60 / 60;
        
        return $timezoneint;
    }
    
    function getCalendartimezone($name, $client)
    {
        static $_cache;
        $_cache = Jojo::getOption('calendar_cache');
        
        $_cache = unserialize($_cache);
        
        if (isset($_cache[$name])) {
            return $_cache[$name];
        }
        
        $gdataCal = new Zend_Gdata_Calendar($client);
        $query = $gdataCal->newEventQuery();
        $query->setUser($calendarid);
        $query->setVisibility('private');
        $query->setProjection('full');
        $query->setOrderby('starttime');
        $query->setSortOrder('ascending'); // either ascending or descending

        $eventFeed = $gdataCal->getCalendarEventFeed($query);
        
        $calendartimezone = $eventFeed->timezone->value;
        
        $_cache[$name] = $calendartimezone;
        Jojo::setOption('calendar_cache', serialize($_cache));
        
        return $_cache[$name];
        
    }
    
    function authenticate($user, $password)
    {
        static $_client;
        
        if (isset($_client)) {
            return $_client;
        }
        
        /* Include the GData classes */
        foreach (Jojo::listPlugins('external/ZendGdata-1.5.2/library/Zend/Loader.php') as $pluginfile) {
            require_once $pluginfile;
            $library = preg_replace('%(.*?)/Zend/Loader\\.php$%', '$1', $pluginfile);
            set_include_path($library);
            break;
        }
        Zend_Loader::loadClass('Zend_Gdata');
        Zend_Loader::loadClass('Zend_Gdata_AuthSub');
        Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
        Zend_Loader::loadClass('Zend_Gdata_Calendar');
        
        $service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;        
        $_client = Zend_Gdata_ClientLogin::getHttpClient($user, $password, $service);
        
        return $_client;
        
    }
    
    function getstartofweek($startDate)
    {
        $startofweek = Jojo::getOption('calendar_start_of_week', 'mon');
        $actualstart = strtotime('last '.$startofweek, $startDate);

    }
    
    function getendofweek($endDate)
    {
        $startofweek = Jojo::getOption('calendar_start_of_week', 'mon');
        $endofweek   = strtolower(date('D', strtotime('+6 day', strtotime('this '.$startofweek.' 12:00'))));
        $actualend   = strtotime('next '.$startofweek, $endDate);

    }
    
}