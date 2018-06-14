<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class ErrorController extends CMS_Controller_Action_Error
{
    
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('main');	
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
                $this->view->title = 'We\'re sorry...';
                $this->view->message = '<p>The resource you requested could not be found.</p>
                <p>It may have been removed or you may have followed a bad link.</p>
                <div class="tagList"><a href="'.$this->view->baseUrl().'/">Visit our Home Page &raquo;</a></div>
                <div class="tagList"><a href="'.$this->view->baseUrl().'/contact/">Contact us to report a problem &raquo;</a></div>';
                break;
            default:
                // application error; display error page, but don't change                
                // status code
                $this->view->title = 'This is embarrassing...';
                $this->view->message = '<p>The application has encountered the following error:</p>
                <p>'.$errors->exception->GetMessage().'</p>
                <div class="tagList"><a href="'.$this->view->baseUrl().'/">Visit our Home Page &raquo;</a></div>
                <div class="tagList"><a href="'.$this->view->baseUrl().'/contact/">Contact us to report a problem &raquo;</a></div>';
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
        $this->view->title = 'We\'re sorry...';
        $this->view->message = '<p>The resource you requested could not be found.</p>
        <p>It may have been removed or you may have followed a bad link.</p>
        <div class="tagList"><a href="'.$this->view->baseUrl().'/">Visit our Home Page &raquo;</a></div>
        <div class="tagList"><a href="'.$this->view->baseUrl().'/contact/">Contact us to report a problem &raquo;</a></div>';
    }
    
	/**
     * This action handles  
     *    - Site Closed Status 
     */
    public function closedAction()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load site status configuration
        $status = new Zend_Config_Ini('../application/configs/config.ini', array('status'));
        $registry->set('status', $status);
        
        $this->setLayout();
        $this->view->title = 'Site Temporarily Unavailable';
        $this->view->message = $registry->status->status->message;
    }
}