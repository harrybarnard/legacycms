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
class Zend_View_Helper_EMapEventList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    /**
     *  
     */
    public function EMapEventList ($venue)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					        ->from(array('e' => 'events'))
    					        ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		        ->where('event_status = ?','published')
				   		        ->where('event_date >= NOW()')
				   		        ->where('event_venue = ?',$venue)
    					        ->order('e.event_date ASC')
    					        ->limit(3,0);
    					   
    	$eventArray = $registry->db->fetchall($select);
    	
    	foreach($eventArray as $event) :
            echo '<div class="map_event">
				  	<a href="'.$this->view->baseUrl().'/events/event/'.$event['event_id'].'/'.urlencode($event['event_title']).'/">'.$event['ecat_title'].' - '.$event['event_title'].'</a><br />
				  	<small>'.$this->view->EDate($event['event_date'],NULL).'</small>
				  </div>';
        endforeach;
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
