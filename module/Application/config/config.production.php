<?php

return array(
    'writableDir' => array(
        'base'            => '',
		'dataCache'       => ROOT_PATH . '/data/data-cache/',
		'log'             => '/var/log/www/timesheet/',
		'styles'          => ROOT_PATH . '/public/styles/',
    ), 
    'errorReport'            => 'min.yao@juneyaokc.com|zhixin.li@juneyaokc.com',
    //'imageServer'            => 'http://www.juneyaokc.com:9999/images/',
    'log' => array(
        'enabled'    => true,
        'file'       => 'error',
        'email'      => true,
    	'emailTimeZone' => 'Asia/Shanghai',
        'slowConnection' => 6,
    ),
    
);

