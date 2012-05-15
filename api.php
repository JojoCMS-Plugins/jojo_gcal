<?php

$_provides['pluginClasses'] = array(
        'Jojo_Plugin_Jojo_gcal' => 'Google calendar - public listing page',
        'Jojo_Plugin_Jojo_gcal_base' => 'Google calendar - public listing page (base version)'
        );

$prefix = Jojo_Plugin_Jojo_gcal::urlPrefix();

Jojo::registerURI("$prefix/[name:string]/[day:integer]-[month:string]-[year:integer]/GMT[timezone:[+-]?[0-9]*]", 'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/1-january-2009/GMT+12/
Jojo::registerURI("$prefix/[name:string]/[month:string]-[year:integer]/GMT[timezone:[+-]?[0-9]*]",               'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/january-2009/GMT+12/
Jojo::registerURI("$prefix/[name:string]/GMT[timezone:[+-]?[0-9]*]",                                             'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/GMT+12/
Jojo::registerURI("$prefix/[name:string]/[day:integer]-[month:string]-[year:integer]",                           'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/1-january-2009/
Jojo::registerURI("$prefix/[name:string]/[month:string]-[year:integer]",                                         'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/january-2009/
Jojo::registerURI("$prefix/[name:string]",                                                                       'Jojo_Plugin_Jojo_gcal'); // calendars/test-calendar/
Jojo::registerURI(null, 'jojo_plugin_jojo_gcal', 'isgcal_return_url');

/* filter for grabbing [[gcal: calendar name]] */
Jojo::addFilter('content', 'contentFilter', 'jojo_gcal');

/* add hook for working with forms for bookings */
Jojo::addHook('contact_form_success', 'processForm', 'jojo_gcal');

Jojo::addHook('contact_form_validation_success', 'bookingValidation', 'jojo_gcal');

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

$_options[] = array(
    'id'          => 'calendar_booking_form_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form (optional)',
    'description' => 'If you are using the a booking form the ID for it',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_date_day_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for day (optional)',
    'description' => 'If you are using the a booking form the ID for the day field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_date_month_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for month (optional)',
    'description' => 'If you are using the a booking form the ID for the month field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_date_year_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for year (optional)',
    'description' => 'If you are using the a booking form the ID for the year field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_start_hour_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for start hour (optional)',
    'description' => 'If you are using the a booking form the ID for the start hour field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_start_minute_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for start minute (optional)',
    'description' => 'If you are using the a booking form the ID for the start minute field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_finish_hour_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for finish hour (optional)',
    'description' => 'If you are using the a booking form the ID for the finish hour field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_finish_minute_id',
    'category'    => 'Calendar',
    'label'       => 'ID of booking form field for finish minute (optional)',
    'description' => 'If you are using the a booking form the ID for the finish minute field',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);

$_options[] = array(
    'id'          => 'calendar_booking_calendar_field_id',
    'category'    => 'Calendar',
    'label'       => 'ID of calendar field (optional)',
    'description' => 'If you are using more than one calendar and have a select this is the id of the form field. Match the calendar name to the select option exactly',
    'type'        => 'integer',
    'default'     => '',
    'options'     => '',
);
