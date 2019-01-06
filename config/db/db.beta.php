<?php
$dbProduction = require ROOT_PATH . '/config/db/db.production.php';
$db = array(
    "cmsdb" => array(
    	"host"        => "192.168.1.121",
        "dbname"      => "timesheet",
        "charset"     => "utf8",
        "username"    => "php",
        "password"    => "php@juneyaokc"
    ), 
	
);
return array_merge($dbProduction,$db);
