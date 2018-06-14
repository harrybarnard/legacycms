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
class Zend_View_Helper_CommentCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function commentCount($slave,$type)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($type == 'U') :
    	
    	    $select = $registry->db->select()
    					       ->from(array('c' => 'comments'))
				   		       ->where('comment_user = ?',$slave);
				   		       
	    else :
    	
    	    $select = $registry->db->select()
    					       ->from(array('c' => 'comments'))
				   		       ->where('comment_slave = ?',$slave)
				   		       ->where('comment_type = ?',$type);
				   		       
	    endif;
    					   
    	$commentArray = $registry->db->fetchall($select);
    	
    	echo count($commentArray);
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
