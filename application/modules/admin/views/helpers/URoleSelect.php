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
class Zend_View_Helper_URoleSelect
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function URoleSelect ()
    {
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    	                       ->from(array('r' => 'users_roles'))
    	                       ->where('r.role_id != 2')
    	                       ->order('r.role_title ASC');

		// Set the data array
		$roleArray = $registry->db->fetchall($select);
		
		foreach($roleArray as $role) :
			echo '<option value="'.$role['role_id'].'">'.$role['role_title'].'</option>';
		endforeach;
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
