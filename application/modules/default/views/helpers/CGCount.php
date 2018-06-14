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
class Zend_View_Helper_CGCount
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function CGCount($slave,$type)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('c' => 'comments'))
    					   ->where('comment_approved = ?','Y')
				   		   ->where('comment_slave = ?',$slave)
				   		   ->where('comment_type = ?',$type);
    					   
    	$commentArray = $registry->db->fetchall($select);
    	
    	$count = count($commentArray);
    	
    	if($count == '0') :
    	    echo 'No Comments';
    	elseif($count == '1') :
    	    echo '1 Comment';
    	else :
    	    echo $count.' Comments';
    	endif;
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
