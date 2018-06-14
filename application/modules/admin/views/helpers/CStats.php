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
class Zend_View_Helper_CStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function cStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('c' => 'comments'));
    					     
    	// Set the data array
		$commentsArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('c' => 'comments'))
    					       ->where('c.comment_approved = ?', 'Y');
    					     
    	// Set the data array
		$approvedArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('c' => 'comments'))
    					       ->where('c.comment_approved = ?', 'N');
    					     
    	// Set the data array
		$disapprovedArray = $registry->db->fetchall($select);
		
		return '<span>Total Comments: '.count($commentsArray).'</span> <span>Approved: '.count($approvedArray).'</span> <span>Disapproved: '.count($disapprovedArray).'</span>';
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

