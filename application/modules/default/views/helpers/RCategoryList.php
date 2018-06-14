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
class Zend_View_Helper_RCategoryList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    public function RCatCount($id)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('r' => 'resources'))
				   		   ->where('resource_category = ?',$id)
				   		   ->where('resource_status = ?','published');
    					   
    	$categoryArray = $registry->db->fetchall($select);
    	
    	return count($categoryArray);
    }
    /**
     *  
     */
    public function RCategoryList ()
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseUrl = $fc->getBaseUrl();
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('c' => 'resources_categories'))
    					   ->order('c.rcat_title ASC');
    					   
    	$catArray = $registry->db->fetchall($select);
    	
    	echo '<div class="rBox">
			<h3>Books &amp; Toys Categories</h3>';
    	foreach($catArray as $cat) :
            echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/resources/category/'.urlencode($cat['rcat_title']).'\/\');">
				  	<a href="'.$this->view->baseUrl().'/resources/category/'.urlencode($cat['rcat_title']).'/">'.$cat['rcat_title'].' ('.$this->RCatCount($cat['rcat_id']).')</a>
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
