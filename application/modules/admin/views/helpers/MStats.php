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
class Zend_View_Helper_MStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function mStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('m' => 'mail'));
    					     
    	// Set the data array
		$mailArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('m' => 'mail'))
    					       ->where('m.mail_status = ?', 'sent');
    					     
    	// Set the data array
		$sentArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('m' => 'mail'))
    					       ->where('m.mail_status = ?', 'draft');
    					     
    	// Set the data array
		$draftArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('g' => 'mail_groups'));
    					     
    	// Set the data array
		$listsArray = $registry->db->fetchall($select);
		
		return '<span>Total Mail: '.count($mailArray).'</span> <span>Sent: '.count($sentArray).'</span> <span>Draft: '.count($draftArray).'</span> <span>Lists: '.count($listsArray).'</span>';
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

