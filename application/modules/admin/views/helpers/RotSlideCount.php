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
class Zend_View_Helper_RotSlideCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function rotSlideCount ($rotator)
    {
        $registry = Zend_Registry::getInstance();
        
		$select = $registry->db->select()
    					       ->from(array('s' => 'rotators_slides'))
    					       ->where('s.rots_rotator = ?', $rotator);
    					     
    	// Set the data array
		$slidesArray = $registry->db->fetchall($select);
		
		return count($slidesArray);
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

