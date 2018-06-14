<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_ErrorController extends CMS_Controller_Action_Error
{
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin-auth');	
	}

	/**
     * This action handles  
     *    - Application errors
     *    - Errors in the controller chain arising from missing 
     *      controller classes and/or action methods
     */
    public function errorAction()
    {
        $this->setLayout();
        
        $errors = $this->_getParam('error_handler');
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found                
                $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                $this->view->title = 'HTTP/1.1 404 Not Found';
                $this->view->message = 'The resource you requested could not be found.';
                break;
            default:
                // application error; display error page, but don't change                
                // status code
                $this->view->title = 'Application Error';
                $this->view->message = $errors->exception;
                break;
        }
    }
    
	/**
     * This action handles  
     *    - Forced 404 errors 
     */
    public function notfoundAction()
    {
        $this->setLayout();
        
        $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
        $this->view->title = 'HTTP/1.1 404 Not Found';
        $this->view->message = 'The resource you requested could not be found.';
    }
    
	/**
     * This action handles  
     *    - Forced 404 errors 
     */
    public function privilegesAction()
    {
        $this->setLayout();
        
        $this->getResponse()->setRawHeader('HTTP/1.1 403 Forbidden');
        $this->view->title = 'HTTP/1.1 403 Forbidden';
        $this->view->message = 'You don\'t have permission to access this resource.';
    }
}