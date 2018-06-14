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
class Zend_View_Helper_ACategoryList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    public function ACatCount($id)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('a' => 'articles'))
				   		   ->where('article_category = ?',$id)
				   		   ->where('article_published <= NOW()')
				   		   ->where('article_status = ?','published');
    					   
    	$categoryArray = $registry->db->fetchall($select);
    	
    	return count($categoryArray);
    }
    /**
     *  
     */
    public function ACategoryList ()
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('c' => 'articles_categories'))
    					   ->order('c.acat_title ASC');
    					   
    	$catArray = $registry->db->fetchall($select);
    	
    	echo '<div class="rBox">
				<h3>Blog Categories</h3>';
    	
    	foreach($catArray as $cat) :
            echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/articles/category/'.urlencode($cat['acat_title']).'\/\');">
				  	<a href="'.$this->view->baseUrl().'/articles/category/'.urlencode($cat['acat_title']).'/">'.$cat['acat_title'].' ('.$this->ACatCount($cat['acat_id']).') </a>
				  </div>';
        endforeach;
        
        echo '</div>
        	<p class="Spacer"></p>';
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
