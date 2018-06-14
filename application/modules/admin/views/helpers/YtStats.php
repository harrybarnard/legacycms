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
class Zend_View_Helper_YtStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function ytStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('y' => 'youtube'));
    					     
    	// Set the data array
		$videosArray = $registry->db->fetchall($select);
		
		return '<span>Total Videos: '.count($videosArray).'</span>';
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

