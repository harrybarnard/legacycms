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
class Zend_View_Helper_PStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function pStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('p' => 'pages'));
    					     
    	// Set the data array
		$resourcesArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('p' => 'pages'))
    					       ->where('p.page_status = ?', 'published');
    					     
    	// Set the data array
		$publishedArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('p' => 'pages'))
    					       ->where('p.page_status = ?', 'draft');
    					     
    	// Set the data array
		$draftArray = $registry->db->fetchall($select);
		
		return '<span>Total Pages: '.count($resourcesArray).'</span> <span>Published: '.count($publishedArray).'</span> <span>Draft: '.count($draftArray).'</span>';
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

