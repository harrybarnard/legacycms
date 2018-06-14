<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class CMS_Controller_Plugin_ErrorSelector extends Zend_Controller_Plugin_Abstract
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$front = Zend_Controller_Front::getInstance();

		//If the ErrorHandler plugin is not registered, bail out
		if( !($front->getPlugin('Zend_Controller_Plugin_ErrorHandler') instanceOf Zend_Controller_Plugin_ErrorHandler) )
			return;

		$error = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');

		//Generate a test request to use to determine if the error controller in our module exists
		$testRequest = new Zend_Controller_Request_HTTP();
		$testRequest->setModuleName($request->getModuleName())
		            ->setControllerName($error->getErrorHandlerController())
		            ->setActionName($error->getErrorHandlerAction());

		//Does the controller even exist?
		if( $front->getDispatcher()->isDispatchable($testRequest) )
		{
			$error->setErrorHandlerModule($request->getModuleName());
		}
	}
}

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin( new CMS_Controller_Plugin_ErrorSelector() );