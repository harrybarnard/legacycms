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
class Zend_View_Helper_ECalendarOptions
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
    public function ECalendarOptions()
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('e' => 'events'))
				   		       ->where('event_status = ?','published')
    					       ->order('e.event_date ASC')
    					       ->limit(1,0);
    					       
    	// Set the data array
		$startArray = $registry->db->fetchall($select);
		
		//if(count($startArray) > 0) :
		    
		$startArray = $startArray[0];
		
		$select = $registry->db->select()
    					       ->from(array('e' => 'events'))
				   		       ->where('event_status = ?','published')
    					       ->order('e.event_date DESC')
    					       ->limit(1,0);
    					       
    	// Set the data array
		$endArray = $registry->db->fetchall($select);
		    
		$endArray = $endArray[0];
        
        $now = strtotime('Now');
		$start = strtotime($startArray['event_date']);
        $end = strtotime($endArray['event_date']);
        
        if($now > $end) :
            $end = $now;
        endif;
        
        echo '<div class="rBox js-on">
			  <h3>Gig Calendar</h3>
			  <select dojoType="dijit.form.FilteringSelect" name="calendararchive" id="calendararchive" autocomplete="false" style="width: 265px;" class="cFormSelect" onchange="goTo(this.value);">
        	      <option value="#" selected>Pick a Month</option>';

	    while ($end > $start) {
	        
	        $count = $this->calendarCount(date("m", $end),date("Y", $end));

            echo '<option value="'.$this->view->baseUrl().'/events/calendar/year/'.date("Y", $end).'/month/'.date("m", $end).'/">'.date("F Y", $end).' ('.$count.')</option>';

            $end = strtotime("-1 month", $end);

	    }
	    
	    echo '</select>
			  </div>
			  <p class="Spacer js-on"></p>';
	    
	    //endif;

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
