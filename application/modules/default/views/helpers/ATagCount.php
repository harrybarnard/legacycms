<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ATagCount helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ATagCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function ATagCount($tag = 'ALL')
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($tag != 'ALL') {
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'tags'),'tag_tag')
    					       ->join(array('a' => 'articles'),'t.tag_slave = a.article_id',array('a.article_status'))
    					       ->where('t.tag_type = ?','A')
    					       ->where('a.article_published <= NOW()')
					           ->where('a.article_status = ?','published')
				   		       ->where('t.tag_tag = ?',$tag);
    	} else {
    	    $select = $registry->db->select()
    	                       ->distinct()
    					       ->from(array('t' => 'tags'),'tag_tag')
				   		       ->join(array('a' => 'articles'),'t.tag_slave = a.article_id',array('a.article_status'))
    					       ->where('t.tag_type = ?','A')
    					       ->where('a.article_published <= NOW()')
					           ->where('a.article_status = ?','published');
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
