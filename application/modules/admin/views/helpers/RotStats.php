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
class Zend_View_Helper_RotStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function rotStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('r' => 'rotators'));
    					     
    	// Set the data array
		$rotatorsArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('s' => 'rotators_slides'));
    					     
    	// Set the data array
		$slidesArray = $registry->db->fetchall($select);
		
		return '<span>Total Rotators: '.count($rotatorsArray).'</span> <span>Total Slides: '.count($slidesArray).'</span>';
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

