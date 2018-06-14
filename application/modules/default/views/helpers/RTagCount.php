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
class Zend_View_Helper_RTagCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function RTagCount($tag = 'ALL')
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($tag != 'ALL') {
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'tags'),'tag_tag')
    					       ->join(array('r' => 'resources'),'t.tag_slave = r.resource_id',array('r.resource_status'))
					           ->where('t.tag_type = ?','R')
					           ->where('r.resource_status = ?','published')
				   		       ->where('t.tag_tag = ?',$tag);
    	} else {
    	    $select = $registry->db->select()
    	                       ->distinct()
    					       ->from(array('t' => 'tags'),'tag_tag')
				   		       ->join(array('r' => 'resources'),'t.tag_slave = r.resource_id',array('r.resource_status'))
					           ->where('t.tag_type = ?','R')
					           ->where('r.resource_status = ?','published');
    	}
    					   
    	$tagArray = $registry->db->fetchall($select);
    	
    	return count($tagArray);
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
