<?php 
/************************ Functions **********************************************/
function errorHandler($errno, $errstr, $errfile, $errline)
{
    $TPL = array(   'errorNumber'       => $errno,
                'errorMessage'      => $errstr,
                'errorLineNumber'   => $errline,
                'errorFileName'     => $errfile,
                'time'              => date("F j, Y, g:i a"));
                
    include ERRORHANDLER_VIEW;   
    exit;
}

function myAutoLoader ($className) 
{

    $classFile = strtolower($className) . '.php';
    
    $locations = array(
        	'/system/classes/',
          '/application/controllers/',
          '/application/models/');
        
        foreach($locations as $location)
        {
            $file = ROOT . $location.$classFile;
            if(file_exists($file)){
                require_once($file);
                return;
            }            
        }

 		trigger_error("Controller file $classFile.php could not be lazy loaded");
 }