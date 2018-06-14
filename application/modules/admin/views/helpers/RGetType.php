<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * CatSelect helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_RGetType
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function rgetType($type)
    {
    	$registry = Zend_Registry::getInstance();
    	
        $select = $registry->db->select()
    					       ->from(array('t' => 'resources_types'))
    					       ->where('t.rtype_id = ?', $type);
    					       
        $tArray = $registry->db->fetchall($select);
		
        $typeArray = $tArray[0];
        
        return $typeArray['rtype_title'];
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
