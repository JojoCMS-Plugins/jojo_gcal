<?php

$table = 'gcal';
$o = 1;

$default_td[$table]['td_displayfield']     = 'name';
$default_td[$table]['td_rolloverfield']    = 'url';
$default_td[$table]['td_orderbyfields']    = 'displayorder, name';
$default_td[$table]['td_topsubmit']        = 'yes';
$default_td[$table]['td_deleteoption']     = 'yes';
$default_td[$table]['td_menutype']         = 'list';
$default_td[$table]['td_help']             = '';

/* ID */
$field = 'calendarid';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'readonly';
$default_fd[$table][$field]['fd_help']     = 'A unique ID, automatically assigned by the system';
$default_fd[$table][$field]['fd_tabname']   = 'Content';

/* name */
$field = 'name';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'text';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_size']     = '50';
$default_fd[$table][$field]['fd_help']     = '';
$default_fd[$table][$field]['fd_tabname']  = 'Content';

/* url */
$field = 'url';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'internalurl';
$default_fd[$table][$field]['fd_options']  = 'calendars';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_size']     = '50';
$default_fd[$table][$field]['fd_help']     = '';
$default_fd[$table][$field]['fd_tabname']  = 'Content';

/* template */
$field = 'template';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'text';
$default_fd[$table][$field]['fd_options']  = '';
$default_fd[$table][$field]['fd_required'] = 'yes';
$default_fd[$table][$field]['fd_size']     = '50';
$default_fd[$table][$field]['fd_help']     = 'Enter the name of the calendar template to use, or leave blank for the default';
$default_fd[$table][$field]['fd_default']  = '';
$default_fd[$table][$field]['fd_tabname']  = 'Content';

/* user */
$field = 'user';
$default_fd[$table][$field]['fd_order']       = $o++;
$default_fd[$table][$field]['fd_name']     = 'Google username';
$default_fd[$table][$field]['fd_type']        = 'text';
$default_fd[$table][$field]['fd_options']     = '';
$default_fd[$table][$field]['fd_required']    = 'no';
$default_fd[$table][$field]['fd_size']        = '50';
$default_fd[$table][$field]['fd_help']        = '';
$default_fd[$table][$field]['fd_tabname']     = 'Content';

/* password */
$field = 'password';
$default_fd[$table][$field]['fd_order']       = $o++;
$default_fd[$table][$field]['fd_name'] = 'Google password';
$default_fd[$table][$field]['fd_type']        = 'text';
$default_fd[$table][$field]['fd_options']     = '';
$default_fd[$table][$field]['fd_required']    = 'no';
$default_fd[$table][$field]['fd_size']        = '50';
$default_fd[$table][$field]['fd_help']        = '';
$default_fd[$table][$field]['fd_tabname']     = 'Content';

/* googleid */
$field = 'googleid';
$default_fd[$table][$field]['fd_order']       = $o++;
$default_fd[$table][$field]['fd_name'] = 'Google calendar ID';
$default_fd[$table][$field]['fd_type']        = 'text';
$default_fd[$table][$field]['fd_options']     = '';
$default_fd[$table][$field]['fd_required']    = 'no';
$default_fd[$table][$field]['fd_size']        = '50';
$default_fd[$table][$field]['fd_help']        = '';
$default_fd[$table][$field]['fd_tabname']     = 'Content';

/* freebusy */
$field = 'freebusy';
$default_fd[$table][$field]['fd_order']       = $o++;
$default_fd[$table][$field]['fd_name'] = 'Free/Busy display';
$default_fd[$table][$field]['fd_type']        = 'list';
$default_fd[$table][$field]['fd_options']     = "yes:Free/Busy display only\nno:Display all details";
$default_fd[$table][$field]['fd_required']    = 'yes';
$default_fd[$table][$field]['fd_help']        = '';
$default_fd[$table][$field]['fd_tabname']     = 'Content';

/* seotitle */
$field = 'seotitle';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'text';
$default_fd[$table][$field]['fd_options']  = '70';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_size']     = '50';
$default_fd[$table][$field]['fd_help']     = '';
$default_fd[$table][$field]['fd_tabname']   = 'Content';

/* Order */
$field = 'displayorder';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'integer';
$default_fd[$table][$field]['fd_help']     = 'Order in which the calendar appears on the main listing';
$default_fd[$table][$field]['fd_tabname']   = 'Content';

/* META Description */
$field = 'metadescription';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_type']     = 'textarea';
$default_fd[$table][$field]['fd_name']     = 'Meta Description';
$default_fd[$table][$field]['fd_rows']     = '3';
$default_fd[$table][$field]['fd_cols']     = '60';
$default_fd[$table][$field]['fd_help']     = 'A good sales oriented description of the page for the Search Engine snippet. Try to keep this within 156 characters, as anything larger will be chopped from the snippet.';
$default_fd[$table][$field]['fd_options']  = 'metadescription';
$default_fd[$table][$field]['fd_tabname']  = '';
$default_fd[$table][$field]['fd_mode']     = 'standard';
$default_fd[$table][$field]['fd_tabname']   = 'Content';

/* TAGS TAB */
$o = 1;

/* Tags */
$field = 'tags';
$default_fd[$table][$field]['fd_order']    = $o++;
$default_fd[$table][$field]['fd_name']     = 'Tags';
$default_fd[$table][$field]['fd_type']     = 'tag';
$default_fd[$table][$field]['fd_required'] = 'no';
$default_fd[$table][$field]['fd_options']  = 'jojo_gallery3';
$default_fd[$table][$field]['fd_showlabel'] = 'no';
$default_fd[$table][$field]['fd_tabname']  = 'Tags';
$default_fd[$table][$field]['fd_help']     = 'A list of words describing the gallery';
$default_fd[$table][$field]['fd_mode']     = 'standard';

