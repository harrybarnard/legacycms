<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class CMS_Controller_Action_Default extends Zend_Controller_Action
{
    
    public function setRefer()
    {
        $_SESSION['curPage'] = $this->getRequest()->getRequestUri();
    }
    
    public function preDispatch()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load site status configuration
        $status = new Zend_Config_Ini('../application/configs/config.ini', array('status'));
        $registry->set('status', $status);
        
        if ($registry->status->status->status == 'closed') :
            $this->_helper->layout->setLayout('main');
            $this->_forward('closed','error','default');
        endif;

        // Set the view renderer
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;

        // Check if user authenticated
        $auth = Zend_Auth::getInstance();
        if ($view->authenticated = $auth->hasIdentity()) {
    	    // Get user details from storage and pass to view
            $view->user = $auth->getStorage()->read();
            $view->role = $view->user->user_role;
            // Build global ACL and pass to view
            $factory = new CMS_Acl_Factory();
            $view->acl = $factory->createGlobalAcl();
        } else {
            $view->role = 2;
            $factory = new CMS_Acl_Factory();
            $view->acl = $factory->createGlobalAcl();
        }
        
        // Set the baseUrl and pass to view
        $view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        
    }
    
}