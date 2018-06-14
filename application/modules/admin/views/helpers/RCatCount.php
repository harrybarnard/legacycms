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
class Zend_View_Helper_RCatCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function rcatCount($id)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('c' => 'resources'))
				   		   ->where('resource_category = ?',$id);
    					   
    	$categoryArray = $registry->db->fetchall($select);
    	
    	echo count($categoryArray);
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
