<?php
/**
 * Jojo CMS - Empty Plugin
 *
 * Copyright 2007-2008 Jojo CMS
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package empty_plugin
 */

/*

The uninstall script is run when the plugin is uninstalled via the admin interface.

The uninstall script may need to do several things, including...

-removing any pages added by the plugin
-removing any database tables added by the plugin (caution with this)
-Removing any options added by the plugin

*/

/* remove the hello page from the menu */
//Jojo::deleteQuery("DELETE FROM `page` WHERE pg_link='Jojo_Plugin_Empty_Plugin'");

//Remove any options the plugin  may have added
//Jojo::removeOption('your option name');