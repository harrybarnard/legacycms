<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ArtCategoryList helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_EMapEventCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    /**
     *  
     */
    public function EMapEventCount ($venue)
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseUrl = $fc->getBaseUrl();
        Zend_Date::setOptions(array('format_type' => 'php'));
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					        ->from(array('e' => 'events'))
				   		        ->where('event_status = ?','published')
				   		        ->where('event_date >= NOW()')
				   		        ->where('event_venue = ?',$venue)
    					        ->order('e.event_date ASC');
    					   
    	$eventArray = $registry->db->fetchall($select);
    	
    	return count($eventArray);
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
