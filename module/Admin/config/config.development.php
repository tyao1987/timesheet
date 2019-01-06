<?php

$configProduction = require ROOT_PATH . '/module/Admin/config/config.production.php';

$config =  array (
    'cmsHost' => 'dev.timesheet.com',
);

return array_merge($configProduction,$config);
