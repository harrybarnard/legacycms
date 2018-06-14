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
class Zend_View_Helper_TList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function TList($slave,$type,$action)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('t' => 'tags'))
				   		   ->where('tag_slave = ?',$slave)
				   		   ->where('tag_type = ?',$type)
				   		   ->order('t.tag_tag ASC');
    					   
    	$tagArray = $registry->db->fetchall($select);
    	
    	if(count($tagArray) > 0) :
    	    foreach($tagArray as $tag) :
    	        echo '<div class="tagList"><a href="'.$this->view->baseUrl().'/'.$action.'/tag/'.urlencode($tag['tag_tag']).'/">'.$tag['tag_tag'].'</a></div>';
    	    endforeach;
    	else :
    	    echo '<div class="tagList">None</div>';
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
