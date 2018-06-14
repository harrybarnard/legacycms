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
class Zend_View_Helper_ECalendarMonths
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    private function calendarCount($month,$year)
    {
    	// setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('e' => 'events'))
    					   ->where('event_published <= NOW()')
				       	   ->where('event_status = ?','published')
				       	   ->where('month(event_date) = ?',$month)
				   		   ->where('year(event_date) = ?',$year)
    					   ->order('e.event_date DESC');
    					   
        $eventsArray = $registry->db->fetchall($select);
        
        return count($eventsArray);
    }
    /**
     *  
     */
    public function ECalendarMonths()
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('e' => 'events'))
				   		       ->where('event_status = ?','published')
    					       ->order('e.event_date ASC')
    					       ->limit(1,0);
    					       
    	// Set the data array
		$endArray = $registry->db->fetchall($select);
		
		$endArray = $endArray[0];
		
		$select = $registry->db->select()
    					       ->from(array('e' => 'events'))
				   		       ->where('event_status = ?','published')
    					       ->order('e.event_date DESC')
    					       ->limit(1,0);
    					       
    	// Set the data array
		$startArray = $registry->db->fetchall($select);
		    
		$startArray = $startArray[0];
        
        $now = strtotime('Now');
		$end = strtotime($endArray['event_date']);
        $start = strtotime($startArray['event_date']);
        
        if($now > $end) :
            $end = $now;
        endif;
        
        echo '<div class="rBox js-on">
			  <h3>Gig Calendar</h3>';

	    while ($start > $end) {
	        
	        $count = $this->calendarCount(date("m", $end),date("Y", $end));
	        
	        if($count != 0) : 
	        
	        echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/events/calendar/year/'.date("Y", $end).'/month/'.date("m", $end).'/);">
				  	<a href="'.$this->view->baseUrl().'/events/calendar/year/'.date("Y", $end).'/month/'.date("m", $end).'/">'.date("F Y", $end).' ('.$count.')</a>
				  </div>';
	        
	        endif;

            $end = strtotime("+1 month", $end);

	    }
	    
	    $count = $this->calendarCount(date("m", $end),date("Y", $end));
	    
	    if($count != 0) :
	        
	    echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/events/calendar/year/'.date("Y", $end).'/month/'.date("m", $end).'/);">
		    <a href="'.$this->view->baseUrl().'/events/calendar/year/'.date("Y", $end).'/month/'.date("m", $end).'/">'.date("F Y", $end).' ('.$count.')</a>
		</div>';
	    
	    endif;
	    
	    echo '</div>';
	    
		echo '<p class="Spacer js-on"></p>';
	    

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
