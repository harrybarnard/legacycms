<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class CMS_Controller_Action_Auth extends Zend_Controller_Action
{
    public function preDispatch()
    {
        // Set the view renderer
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

        // Check if user authenticated
        $auth = Zend_Auth::getInstance();
        if ($view->authenticated = $auth->hasIdentity()) {
    	    // Get user details from storage and pass to view
            $view->user = $auth->getStorage()->read();
        }
        
        // Set the baseUrl and pass to view
        $view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        
    }
    
}