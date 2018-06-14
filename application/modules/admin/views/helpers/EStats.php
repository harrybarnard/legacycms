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
class Zend_View_Helper_EStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function eStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('e' => 'events'))
    					       ->where('event_date >= NOW()');
    					     
    	// Set the data array
		$eventsArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('e' => 'events'))
    					       ->where('event_date >= NOW()')
    					       ->where('e.event_status = ?', 'published');
    					     
    	// Set the data array
		$publishedArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('e' => 'events'))
    					       ->where('event_date >= NOW()')
    					       ->where('e.event_status = ?', 'draft');
    					     
    	// Set the data array
		$draftArray = $registry->db->fetchall($select);
		
		return '<span>Future Events: '.count($eventsArray).'</span> <span>Published: '.count($publishedArray).'</span> <span>Draft: '.count($draftArray).'</span>';
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

