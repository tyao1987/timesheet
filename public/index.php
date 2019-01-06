<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
define('ROOT_PATH', dirname(dirname(__FILE__)));

chdir(ROOT_PATH);
// 定义应用运行环境，可以在 .htaccess 中设置 SetEnv APPLICATION_ENV development
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV')?getenv('APPLICATION_ENV'):'production'));

defined('ACTIVE_MODULE')
|| define('ACTIVE_MODULE', (getenv('ACTIVE_MODULE')?getenv('ACTIVE_MODULE'):'application'));

// 不是产品环境则允许显示错误，以方便调试
if (APPLICATION_ENV == 'development') {
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
	ini_set('display_startup_errors', 1);
	ini_set('display_errors', 1);
} else {
	// 是产品环境则不允许显示错误
	ini_set('display_startup_errors', 0);
	ini_set('display_errors', 0);
}

// Setup autoloading
require 'init_autoloader.php';

Test\Util\Timer::start('ALL');

try {
    // Run the application!
    $application = Zend\Mvc\Application::init(require 'config/module/' . ACTIVE_MODULE . '.php');
    $application->run();
} catch (\Exception $e) {
    if(ACTIVE_MODULE != 'application'){
        $loader->add('Application', ROOT_PATH . '/module/Application/src/');
        $data = Test\Data::getInstance();
        if (!$data->has('config')) {
            $config = include ROOT_PATH . "/module/Application/config/config." . APPLICATION_ENV . ".php";
            $data->set('config', $config, true);
        }
        \Application\Exception::log($e);
        if(APPLICATION_ENV == 'local'){
            echo \Application\Exception::log($e,true);
        }else{
            $url = "/";
            header("Location: ".$url."?referer=" . $_SERVER['REQUEST_URI'], true, 302);
        }
        exit;
    }
}

Test\Util\Timer::end('ALL');

if (!$application->getRequest()->isXmlHttpRequest() && ACTIVE_MODULE == "application"
        && (APPLICATION_ENV == "development" || (boolean)$_GET['debug_time'])) {
   // echo Test\Util\Timer::show();
}
