<?php


$table = 'gcal';
$query = "
    CREATE TABLE {gcal} (
        `calendarid` int(11) NOT NULL auto_increment,
        `name` varchar(255) NOT NULL default '',
        `url` varchar(255) NOT NULL default '',
        `template` varchar(255) NOT NULL default '',
        `seotitle` varchar(255) NOT NULL default '',
        `displayorder` int(11) NOT NULL default '0',
        `metadescription` varchar(255) NOT NULL default '',
        `tags` varchar(255) NOT NULL default '',
        `user` varchar(255) NOT NULL default '',
        `password` varchar(255) NOT NULL default '',
        `googleid` varchar(255) NOT NULL default '',
        `freebusy` enum('yes','no') NOT NULL default 'no',
         PRIMARY KEY  (`calendarid`),
         FULLTEXT KEY `title` (`name`)
    ) TYPE=MyISAM ;";

/* Check table structure */
$result = Jojo::checkTable($table, $query);

/* Output result */
if (isset($result['created'])) {
    echo sprintf("jojo_gcal: Table <b>%s</b> Does not exist - created empty table.<br />", $table);
}

if (isset($result['added'])) {
    foreach ($result['added'] as $col => $v) {
        echo sprintf("jojo_gcal: Table <b>%s</b> column <b>%s</b> Does not exist - added.<br />", $table, $col);
    }
}

if (isset($result['different'])) Jojo::printTableDifference($table,$result['different']);