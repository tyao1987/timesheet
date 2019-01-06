<?php
$dbProduction = require ROOT_PATH . '/config/db/db.production.php';
$db = array(
    "cmsdb" => array(
        "host"        => "127.0.0.1", 
        "dbname"      => "timesheet", 
        "charset"     => "utf8", 
        "username"    => "root", 
        "password"    => "123456"
    ), 
	
);
return array_merge($dbProduction,$db);
