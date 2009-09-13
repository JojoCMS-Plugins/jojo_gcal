<?php

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_gcal' => 'Google calendar - public listing page'
        );

$prefix = Jojo_Plugin_Jojo_gcal::urlPrefix();

Jojo::registerURI("$prefix/[name:string]/[day:integer]-[month:string]-[year:integer]/GMT[timezone:[+-]?[0-9]*]", 'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/1-january-2009/GMT+12/
Jojo::registerURI("$prefix/[name:string]/[month:string]-[year:integer]/GMT[timezone:[+-]?[0-9]*]",               'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/january-2009/GMT+12/
Jojo::registerURI("$prefix/[name:string]/GMT[timezone:[+-]?[0-9]*]",                                             'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/GMT+12/
Jojo::registerURI("$prefix/[name:string]/[day:integer]-[month:string]-[year:integer]",                           'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/1-january-2009/
Jojo::registerURI("$prefix/[name:string]/[month:string]-[year:integer]",                                         'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/january-2009/
Jojo::registerURI("$prefix/[name:string]",                                                                       'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/

/* filter for grabbing [[gcal: calendar name]] */
Jojo::addFilter('content', 'contentFilter', 'jojo_gcal');

/* add an icon onto the editors for inserting Youtube videos */
$vars = array('url'=>array('name'=>'url','description'=>'Calendar URL or ID'));
$calendar = array(
                'name'=>'Calendar',
                'format'=>'[[gcal: [url]]]',
                'description'=>'',
                'vars'=>$vars,
                'icon'=>'images/calendar.png'
                );
Jojo::addContentVar($calendar);

$_options[] = array(
    'id'          => 'calendar_start_of_week',
    'category'    => 'Calendar',
    'label'       => 'Start of Week',
    'description' => 'The calendar will start on the selected day of the week, usually Monday or Sunday.',
    'type'        => 'radio',
    'default'     => 'mon',
    'options'     => 'mon,tue,wed,thu,fri,sat,sun',
);

$_options[] = array(
    'id'          => 'calendar_cache',
    'category'    => 'Calendar',
    'label'       => 'Calendar time zone',
    'description' => 'This records the time zones of each calendar.',
    'type'        => 'textarea',
    'default'     => '',
    'options'     => '',
);