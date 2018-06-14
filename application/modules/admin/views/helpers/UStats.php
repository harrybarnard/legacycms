<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * AStats helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_UStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function uStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('u' => 'users'));
    					     
    	// Set the data array
		$usersArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_status = ?', 'active');
    					     
    	// Set the data array
		$activeArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_status = ?', 'inactive');
    					     
    	// Set the data array
		$inactiveArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_status = ?', 'suspended');
    					     
    	// Set the data array
		$suspendedArray = $registry->db->fetchall($select);
		
		return '<span>Total Users: '.count($usersArray).'</span> <span>Active: '.count($activeArray).'</span> <span>Inactive: '.count($inactiveArray).'</span> <span>Suspended: '.count($suspendedArray).'</span>';
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

