<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * CatSelect helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_MUserDetails
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function MUserDetails($user)
    {
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_id = ?', $user)
    					       ->limit(1,0);

		$userArray = $registry->db->fetchall($select);
		    
		$userArray = $userArray['0'];
		    
		return $userArray['user_alias']. ' ('.$userArray['user_email'].')';
    	
    }
    /**
     * Sets the view field 
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
