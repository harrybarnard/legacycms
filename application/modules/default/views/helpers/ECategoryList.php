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
class Zend_View_Helper_ECategoryList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    public function ECatCount($id)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('e' => 'events'))
				   		   ->where('event_category = ?',$id)
				   		   ->where('event_date >= NOW()')
				   		   ->where('event_status = ?','published');
    					   
    	$categoryArray = $registry->db->fetchall($select);
    	
    	return count($categoryArray);
    }
    /**
     *  
     */
    public function ECategoryList ()
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('c' => 'events_categories'))
    					   ->order('c.ecat_title ASC');
    					   
    	$catArray = $registry->db->fetchall($select);
    	
    	echo '<div class="rBox">
			<h3>Event Categories</h3>';
    	
    	foreach($catArray as $cat) :
            echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/events/category/'.urlencode($cat['ecat_title']).'\/\');">
				  	<a href="'.$this->view->baseUrl().'/events/category/'.urlencode($cat['ecat_title']).'/">'.$cat['ecat_title'].' ('.$this->ECatCount($cat['ecat_id']).') </a>
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
