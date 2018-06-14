<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

// Autoload Composer
require __DIR__ . '/../vendor/autoload.php';

set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . '../application/models' . PATH_SEPARATOR . get_include_path());

require_once 'initializer.php';

// Prepare the front controller. 
$frontController = Zend_Controller_Front::getInstance(); 

// Change to 'production' parameter under production environemtn
$frontController->registerPlugin(new Initializer('development'));

// Dispatch the request using the front controller. 
$frontController->dispatch(); 