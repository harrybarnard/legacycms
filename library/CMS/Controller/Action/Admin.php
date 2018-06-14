<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class CMS_Controller_Action_Admin extends Zend_Controller_Action
{
    
    public function setRefer()
    {
        $_SESSION['curPage'] = $this->getRequest()->getRequestUri();
    }
    
    public function preDispatch()
    {
        // Set the view renderer
        $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
        
        // Set the baseUrl and pass to view
        $view->baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        
        $sessName = "PHPSESSID";
        $sessOptions = array('name' => $sessName);

        // Flash has problems with cookies so we pass the PHPSESSID variable via get
        // it'll be injected if it doesn't exist in _SERVER["HTTP_COOKIE"] e.g. '; PHPSESSID=hdi5u83hfnu7ltlvp5q3bb53k4'
        if ((stripos($_SERVER['REQUEST_URI'], '__tkn') !== false) && preg_match('#__tkn/([a-z\d]{25,32})#si', $_SERVER['REQUEST_URI'], $matches) && (stripos($_SERVER["HTTP_COOKIE"], $matches[1]) === false)) {
            $sid = $matches[1];

            $prefix = '';
            if (!empty($_SERVER["HTTP_COOKIE"])) {
                $prefix = '; ';
            }

            $_SERVER["HTTP_COOKIE"] .= $prefix . $sessName . '=' . $sid;
            $_COOKIE[$sessName] = $sid;

            Zend_Session::setId($sid);
        }

        Zend_Session::setOptions($sessOptions);

        // Check if user authenticated
        $auth = Zend_Auth::getInstance();
        if ($view->authenticated = $auth->hasIdentity()) {
    	    // Get user details from storage and pass to view
            $view->user = $auth->getStorage()->read();
            // Build global ACL and pass to view
            $factory = new CMS_Acl_Factory();
            $view->acl = $factory->createGlobalAcl(); 
            if(!$this->view->acl->isAllowed($this->view->user->user_role, 'gadmin')) :
                $this->_forward('privileges','error','admin');
		    endif;
        } else {
            $this->_helper->redirector('login','auth','admin');
        }
    }

}