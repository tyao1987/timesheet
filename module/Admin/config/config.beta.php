<?php

$configProduction = require ROOT_PATH . '/module/Admin/config/config.production.php';

$config =  array (
 	'cmsHost' => 'beta.timesheet.com',
);

return array_merge($configProduction,$config);
