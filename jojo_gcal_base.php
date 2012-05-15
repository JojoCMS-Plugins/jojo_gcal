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

class Jojo_Plugin_Jojo_gcal_base extends Jojo_Plugin
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
            $e = array();
            foreach ($events as $event) {
                if (($event['start'] > $t) && ($event['start'] < ($t + (60 * 60 * 24)))) {$e[] = $event;}
            }
            $days[] = array('timestamp' => $t, 'day' => date('j', $t), 'weekday' => strtolower(date('D', $t)), 'month' => date('F', $t), 'events' => $e);
            $t = $t + (60 * 60 * 24);
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
    
    function bookingValidation($errors, $fields){
       
        // Prep
        // change the months into two digits
        $months = array('January'=>1, 'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);
        // change the hours into 24 hours
        $hours = array('8'=>'08', '9'=>'09', '10'=>'10', '11'=>'11', '12'=>'12', '1'=>'13', '2'=>'14', '3'=>'15', '4'=>'16', '5'=>'17');
        
        // Loop through fields to pull out the ones we need
        $bookingDay = '0';
        $bookingMonth = '0';
        $bookingYear = '0';
        $bookingStartHour = '0';
        $bookingStartMinute = '0';
        $bookingFinishHour = '0';
        $bookingFinishMinute = '0';
        $bookingCalendar = '';
        
        foreach($fields as $key=>$value){
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_date_day_id')){
                $bookingDay = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_date_month_id')){
                $bookingMonth = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_date_year_id')){
                $bookingYear = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_start_hour_id')){
                $bookingStartHour = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_start_minute_id')){
                $bookingStartMinute = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_finish_hour_id')){
                $bookingFinishHour = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_finish_minute_id')){
                $bookingFinishMinute = $value['value'];
            }
            if($value['formfield_id'] == Jojo::getOption('calendar_booking_calendar_field_id')){
                $bookingCalendar = $value['value'];
            }
            
        }
        
        // Validate
        // 1. it is at least an hour in the future
        // 2. it starts before it finishes
        // 3. it is at least an hour long
        // 4. its on a monday to friday
        // 5. it doesn't start before another booking finishes 
        // 6. it doesn't start before another booking starts and finish after it starts 
        
        if(mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) < (time() + 3600)){
            $errors[] = "Must be at least an hour in the future";
        }
        
        if(mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) > mktime($hours[$bookingFinishHour],$bookingFinishMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear)){
                $errors[] = "Must start before it finishes";
        }
        
        if((mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) + 3599 ) > mktime($hours[$bookingFinishHour],$bookingFinishMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear)){
                $errors[] = "Must be at least an hour long";
        }
        
        if(date('l', mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear)) == 'Saturday' || date('l', mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear)) == 'Sunday'){
            $errors[] = "Must be on a weekday";
        }
        
        // Get events for day and check that they don't clash
        
        $calendars = Jojo::selectQuery("SELECT * FROM {gcal} ORDER BY displayorder");
        
        // Loop through each calendar
        foreach($calendars as $key=>$value){
            if($bookingCalendar == $value['name']){
                $calendarname = $value['name'];
                
                $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendarname);
                $timezone = Jojo_Plugin_Jojo_gcal::getCalendartimezone($calendarname, Jojo_Plugin_Jojo_gcal::authenticate($calendar['user'], $calendar['password']));
                
                $timezone = Jojo_Plugin_Jojo_gcal::getTimezoneint($timezone);
                
                $start = strtotime($bookingDay.' '.$months[$bookingMonth].' '.$bookingYear);
                
                $end = strtotime('+1 day'.$bookingDay.' '.$months[$bookingMonth].' '.$bookingYear);
                
                $events = Jojo_Plugin_Jojo_gcal::getEvents($calendarname, $start, $end, $timezone);
                
                foreach($events as $event){
                    
                    if(mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) >= $event['start'] && mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) < $event['end']){
                        $errors[] = "There is already a booking for this time. Please check again.";
                    }
                    if(mktime($hours[$bookingStartHour],$bookingStartMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) < $event['start'] && mktime($hours[$bookingFinishHour],$bookingFinishMinute,0,$months[$bookingMonth], $bookingDay, $bookingYear) > $event['start']){
                        $errors[] = "There is already a booking for this time. Please check again.";
                    }
                    
                }
            }
        }
        
        return array($errors, $fields);
    }
    
    function addEvent($eventData, $calendarName, $uri = ''){
        
        // Note about $uri
        // If you are adding to the non default calendar then you will need this otherwise not.
        
        $calendar = Jojo_Plugin_Jojo_gcal::getCalendarData($calendarName);
        $client = Jojo_Plugin_Jojo_gcal::authenticate($calendar['user'], $calendar['password']);
        
        $gcal = new Zend_Gdata_Calendar($client);
        
        // validate input
        if (empty($eventData['title'])) {
            $error[] = 'ERROR: Missing title';
        } 
        
        if (!checkdate($eventData['sdate_mm'], $eventData['sdate_dd'], $eventData['sdate_yy'])) {
            $error[] = 'ERROR: Invalid start date/time';        
        }
        
        if (!checkdate($eventData['edate_mm'], $eventData['edate_dd'], $eventData['edate_yy'])) {
            $error[] = 'ERROR: Invalid end date/time';        
        }
        
        $title = htmlentities($eventData['title']);
        $start = date(DATE_ATOM, mktime($eventData['sdate_hh'], $eventData['sdate_ii'], 
        0, $eventData['sdate_mm'], $eventData['sdate_dd'], $eventData['sdate_yy']));
        $end = date(DATE_ATOM, mktime($eventData['edate_hh'], $eventData['edate_ii'], 
        0, $eventData['edate_mm'], $eventData['edate_dd'], $eventData['edate_yy']));
        
        $event = $gcal->newEventEntry();        
        $event->title = $gcal->newTitle($title);        
        $when = $gcal->newWhen();
        $when->startTime = $start;
        $when->endTime = $end;
        $event->when = array($when); 
        if(strlen($uri) > 0){
            $gcal->insertEvent($event, $uri);   
        } else {
            $gcal->insertEvent($event);
        }
    }
    
    function processForm($formID, $res)
    {
        // Get here after processing form.
        
        
        
    }
    
    function processReturn()
    {
        // CUSTOM CALL TO PAYPAL TO CONFIRM PAYMENT - NEED TO ADD GENERIC OPTIONS FOR OTHER PAYMENT GATEWAYS
        $result = call_user_func(array('jojo_plugin_jojo_cart_paypal', 'process'));
                    
        if($result['success']){
            
            //pull data out of db
            $formSubmissionID = trim($_POST['custom']);
            $formSubmission = Jojo::selectQuery("SELECT * FROM {formsubmission} WHERE formsubmissionid = $formSubmissionID ");
            $content = $formSubmission[0]['content'];
            $fields = unserialize($content);
            if($fields[15]['value'] == 'submitted'){
                // change months into two digits
                $months = array('January'=>01, 'February'=>02, 'March'=>03, 'April'=>04, 'May'=>05, 'June'=>06, 'July'=>07, 'August'=>08, 'September'=>09, 'October'=>10, 'November'=>11, 'December'=>12);
                
                // change the hours into 24 hours
                $hours = array('8'=>'08', '9'=>'09', '10'=>'10', '11'=>'11', '12'=>'12', '1'=>'13', '2'=>'14', '3'=>'15', '4'=>'16', '5'=>'17');
                
                $calendarName = $fields[6]['value'];
                $eventData['title'] = $fields[1]['value'];//company
                $eventData['sdate_dd'] = $fields[7]['value'];//day
                $eventData['sdate_mm'] = $months[$fields[8]['value']];//month
                $eventData['sdate_yy'] = $fields[9]['value'];//year
                $eventData['edate_dd'] = $fields[7]['value'];//day
                $eventData['edate_mm'] = $months[$fields[8]['value']];//month
                $eventData['edate_yy'] = $fields[9]['value'];//year
                $eventData['edate_hh'] = $hours[$fields[12]['value']];//finish hour
                $eventData['edate_ii'] = $fields[13]['value'];//finish minute
                $eventData['sdate_hh'] = $hours[$fields[10]['value']];//start hour
                $eventData['sdate_ii'] = $fields[11]['value'];//start minute
                
                //generate uri if not default calendar - CURRENTLY CUSTOM!
                $uri = '';
                if($calendarName == "Executive Board Room")
                    $uri = "https://www.google.com/calendar/feeds/cgrmk9binj4fvsottio7pdigls%40group.calendar.google.com/private/full";
                
                //add event
                self::addEvent($eventData, $calendarName, $uri);
            
                //send email
            
                $to_name = Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME);
                $from_name = Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME);
                $to_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
                $from_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
                $subject = Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME) . " - Booking Calendar - Deposit Completed";
                $subject = mb_convert_encoding($subject, 'HTML-ENTITIES', 'UTF-8');
                $sender_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
                
                $message = "";
                $htmlmessage = "<html><head></head><body>";
                
                //setup message to send
                $message  = '';
                foreach ($fields as $f) {
                    if (isset($f['displayonly'])) { continue; };
                    if ($f['type'] == 'note') { continue; };
                    if ($f['type'] == 'heading') {
                        $message .=  "\r\n" . $f['value'] . "\r\n";
                        for ($i=0; $i<strlen($f['value']); $i++) {
                            $message .= '-';
                        }
                        $message .= "\r\n";
                    } else {
                        $message .= (isset($f['showlabel']) && $f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? "\r\n" . $f['value'] . "\r\n\r\n" : $f['value'] . "\r\n" );
                    }
                }
                
                $message .= Jojo::emailFooter();
        
                $messagefields = '';
                foreach ($fields as $f) {
                    if (isset($f['displayonly']) || $f['type'] == 'note') { continue; };
                    if ($f['type'] == 'heading') {
                        $messagefields .=  '<h' . ($f['size'] ? $f['size'] : '3') . '>' . $f['value'] . '</h' . ($f['size'] ? $f['size'] : '3') . '>';
                    } else {
                        $messagefields .= '<p>' . (isset($f['showlabel']) && $f['showlabel'] ? $f['display'] . ': ' : '' ) . ($f['type'] == 'textarea' || $f['type'] == 'checkboxes' ? '<br>' . nl2br($f['value']) : $f['value'] ) . '</p>';
                    }
                }
                
                //$htmlmessage .= "<h2>Booking Completed - Deposit Accepted</h2>";
                
                $htmlmessage =  $messagefields . '<p>' . nl2br(Jojo::emailFooter()) . '</p>';
                
                // basic inline styling for supplied content
                $htmlcss = '';
                $htmlmessage = str_replace('<p>', '<p style="font-size:13px;' . $htmlcss . '">', $htmlmessage);
                $htmlmessage = str_replace('<td>', '<td style="font-size:13px;' . $htmlcss . '">', $htmlmessage);
                $htmlmessage = str_replace(array('<h1>', '<h2>', '<h3>'), '<p style="font-size: 16px;' . $htmlcss . '">', $htmlmessage);
                $htmlmessage = str_replace(array('<h4>','<h5>', '<h6>'), '<p style="font-size: 14px;' . $htmlcss . '">', $htmlmessage);
                $htmlmessage = str_replace(array('</h1>', '</h2>', '</h3>', '</h4>','</h5>', '</h6>'), '</p>', $htmlmessage);
                
                
                global $smarty;
                $smarty->assign('htmlmessage', $htmlmessage);
                $htmlmessage  = $smarty->fetch('jojo_contact_autoreply.tpl');   
                
                
                //send email
                Jojo::simpleMail($to_name, $to_email, $subject, $message, $from_name, $from_email, $htmlmessage, $sender_email);
                
                //update the db so that we don't add it again or email it again.
                $fields[15]['value'] = 'complete';
                
                $res = Jojo::updateQuery("UPDATE {formsubmission} SET `content` = ? WHERE `formsubmissionid` = ? LIMIT 1", array(serialize($fields), $formSubmissionID));
            }
        }
        
    }
    
    function notifications()
    {
        // Pull submissions out of database
        // (have to pull all of them as can't sort them on the way out as data is in a serialized array in one field)
        $submissions = Jojo::selectQuery("SELECT * FROM {formsubmission} WHERE form_id = ? ", array(Jojo::getOption('calendar_booking_form_id')));
            //echo "results = <pre>";print_r($submissions);echo "</pre>";die();exit();
        
        // change the hours into 24 hours
        $hours = array('8'=>'08', '9'=>'09', '10'=>'10', '11'=>'11', '12'=>'12', '1'=>'13', '2'=>'14', '3'=>'15', '4'=>'16', '5'=>'17');
        
        // change the months into two digits
        $months = array('January'=>1, 'February'=>2, 'March'=>3, 'April'=>4, 'May'=>5, 'June'=>6, 'July'=>7, 'August'=>8, 'September'=>9, 'October'=>10, 'November'=>11, 'December'=>12);
            
        // Loop through then and if any are in the next hour send an email
        foreach($submissions as $key=>$value){
            $fields = unserialize($value['content']);
            
            //change time to timestamp for event check if it between 1 and 1.5 hours from now
            $eventStart = mktime($hours[$fields[10]['value']],$fields[11]['value'],0,$months[$fields[8]['value']], $fields[7]['value'], $fields[9]['value']);
            if($eventStart > time()+3600 && $eventStart < time()+5400){
                
                $from_name = Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME);
                $from_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
                $subject = Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME) . ' Meeting Notification';
                $message = 'Hi '. $fields[0]['value'] . ',\n Your meeting at ' . Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME) . ' will start shortly \n Thank you';
                $to_name = $fields[1]['value'];
                $to_email = $fields[2]['value'];
                $htmlmessage = 'Hi '. $fields[0]['value'] . ',<br />Your meeting at ' . Jojo::either(_CONTACTNAME, _FROMNAME, _WEBNAME) . ' will start shortly<br /> Thank you';
                $sender_email = Jojo::either(_CONTACTADDRESS, _FROMADDRESS, _WEBMASTERADDRESS);
                
                Jojo::simpleMail($to_name, $to_email, $subject, $message, $from_name, $from_email, $htmlmessage, $sender_email);
                
            }
            
        }
        
        
    }
    
    static public function isgcal_return_url($uri)
    {
        
        // Paypal IPN Link
        if (preg_match('#gcal-booking-return#', $uri, $matches)) {
            
            self::processReturn();
            return true;
        }
        
        // Cron job send notification emails
        if (preg_match('#gcal-booking-notifications#', $uri, $matches)) {
            
            self::notifications();
            return true;
        }
        
        return false;
        
    }
    
}
