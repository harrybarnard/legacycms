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
class Zend_View_Helper_TCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function TCount($tag = 'ALL',$type = 'ALL')
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($tag != 'ALL') {
    	    if ($type != 'ALL') {
    	        $select = $registry->db->select()
    					           ->from(array('t' => 'tags'),'tag_tag')
				   		           ->where('tag_tag = ?',$tag)
				   		           ->where('tag_type = ?',$type);
    	    } else {
    	        $select = $registry->db->select()
    	                           ->distinct()
    					           ->from(array('t' => 'tags'),'tag_tag')
				   		           ->where('tag_tag = ?',$tag);
    	    }
    	} else {
    	    if ($type != 'ALL') {
    	        $select = $registry->db->select()
    	                           ->distinct()
    					           ->from(array('t' => 'tags'),'tag_tag')
				   		           ->where('tag_type = ?',$type);
    	    } else {
    	        $select = $registry->db->select()
    	                           ->distinct()
    					           ->from(array('t' => 'tags'),'tag_tag');
    	    }
    	}
    					   
    	$tagArray = $registry->db->fetchall($select);
    	
    	return count($tagArray);
    }
	/**
     *  
     */
    public function tagTypeCount($type = 0)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($type != 0) {
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'tags'))
				   		       ->where('tag_type = ?',$type);
    	} else {
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'tags'));
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
