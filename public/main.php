<?php	

session_start();
if (!isset($_SESSION['startTimeOfSession'])){$_SESSION['startTimeOfSession'] = time();}

//error reporting
ini_set('error_reporting',E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

//makes the URL neat and small
$_SERVER["PHP_SELF"]  = basename($_SERVER["PHP_SELF"]);

//Paths to inportant system files
define('ROOT', dirname(dirname(__FILE__)));
define('ERRORHANDLER_VIEW',ROOT.'/system/errorPages/errorHandler.view.php');
define('DATABASEERROR_VIEW',ROOT.'/system/errorPages/dataBaseError.view.php');

define('LOGFILE',ROOT.'/system/logFiles/logfile.txt');

//default values
define ('DEFAULT_CONTROLLER','home');
define ('DEFAULT_METHOD','index');

//Eager load all configuration files
 foreach (glob(ROOT.'/system/config/*.php') as $filename) {
     require_once $filename;
 }

$GLOBALS['config'] =& $config;


//General and program errors reported here....
set_error_handler('errorHandler',E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));

// Set function for use as autoloader - defined in config/functions.php
spl_autoload_register('myAutoLoader');

parsePrettyPath(); // Converts path into control parameters - defined in config/functions.php

//default conditions

foreach ($_REQUEST['app'] as $controller => $methods) {

	$controllerName = ($controller == 'default' ? DEFAULT_CONTROLLER : $controller);	

	$controllerName = ucfirst($controllerName);
	$controllerObj = new $controllerName; 

	foreach ($methods as $method => $args) {

		$method = ($method == 'default' ? DEFAULT_METHOD : $method);	

		if (method_exists($controllerObj, $method)) {
			 call_user_func(array($controllerObj,$method),$args);
		} else {
			 trigger_error("Non-existent  method has been called: $controllerName, $method");
		}
	}
}


