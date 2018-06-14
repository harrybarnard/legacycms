<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * RTagCount helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_RTypeCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function RTypeCount($category)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($category != NULL) {
    	    $select = $registry->db->select()
    					       ->from(array('r' => 'resources'),'resource_type')
    					       ->where('r.resource_type = ?','1')
					           ->where('r.resource_status = ?','published')
				   		       ->where('r.resource_category = ?',$category);
		    $bookArray = $registry->db->fetchall($select);
		    
		    $select = $registry->db->select()
    					       ->from(array('r' => 'resources'),'resource_type')
    					       ->where('r.resource_type = ?','2')
					           ->where('r.resource_status = ?','published')
				   		       ->where('r.resource_category = ?',$category);
		    $toyArray = $registry->db->fetchall($select);
    	
    	    return count($bookArray).' Books - '.count($toyArray).' Toys';
    	} else {
		    return false;
    	}
    					   
    	
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
