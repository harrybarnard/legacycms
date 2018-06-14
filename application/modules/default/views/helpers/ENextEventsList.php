<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ArtRecentArtList helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ENextEventsList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function ENextEventsList ($links = false)
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseUrl = $fc->getBaseUrl();
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					       ->from(array('e' => 'events'))
    					       ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
    					       ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		       ->where('event_status = ?','published')
				   		       ->where('event_date >= NOW()')
    					       ->order('e.event_date ASC')
    					       ->limit(5, 0);
    					   
    	$eventsArray = $registry->db->fetchall($select);
    	
    	if(count($eventsArray) > 0) :
    	
    	    echo '<div class="rBox">
				<h3>Outreach</h3>';
    	
    	    foreach($eventsArray as $event) :
    	        if($event['venue_id'] != '1') :
                    echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/events/event/'.$event['event_id'].'/'.urlencode($event['event_title']).'\/\');">
				  	    <a href="'.$this->view->baseUrl().'/events/event/'.$event['event_id'].'/'.urlencode($event['event_title']).'/">'.$event['ecat_title'].' - '.$event['event_title'].', '.$event['venue_title'].', '.$event['venue_city'].'</a>
				  	    <small>'.$this->view->EDate($event['event_date'],$event['event_end']).'</small>
				      </div>';
                else :
                    echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/events/event/'.$event['event_id'].'/'.urlencode($event['event_title']).'\/\');">
				  	    <a href="'.$this->view->baseUrl().'/events/event/'.$event['event_id'].'/'.urlencode($event['event_title']).'/">'.$event['ecat_title'].' - '.$event['event_title'].'</a>
				  	    <small>'.$this->view->EDate($event['event_date'],$event['event_end']).'</small>
				      </div>';
                endif;
            endforeach;
            if($links == true) :
                echo '<div class="rMore">
					<a href="'.$this->view->baseUrl().'/events/">More Events &raquo;</a>
				</div>';
            endif;
            echo '</div>
        	<p class="Spacer"></p>';
        
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
