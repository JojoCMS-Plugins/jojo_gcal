<?php
/* Calendars */
$data = Jojo::selectQuery("SELECT * FROM {page}  WHERE pg_link='Jojo_Plugin_Jojo_gcal'");
if (!count($data)) {
    echo "Jojo_Plugin_Jojo_gcal: Adding <b>Google Calendar</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Calendars', pg_link='Jojo_Plugin_Jojo_gcal', pg_url='calendars'");
}

/* Edit Calendars */
$data = Jojo::selectQuery("SELECT * FROM {page} WHERE pg_url='admin/edit/gcal'");
if (!count($data)) {
    echo "jojo_gallery3: Adding <b>Edit Calendars</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title='Edit Calendars', pg_link='Jojo_Plugin_Admin_Edit', pg_url='admin/edit/gcal', pg_parent = ?, pg_order=4", array($_ADMIN_CONTENT_ID));
}