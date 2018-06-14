<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * DynThumbnail helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ECalendar
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    public function generateCalendar($month,$year) {
        define("ADAY", (60*60*24));
        if ((!isset($month)) || (!isset($year))) {
            $nowArray = getdate();
	        $month = $nowArray["mon"];
	        $year = $nowArray["year"];
        }
        $start = mktime (12, 0, 0, $month, 1, $year);
        $firstDayArray = getdate($start);
        
        if ($month == 12) :
            $nextmonth = 1;
            $nextyear = $year + 1;
        else :
            $nextmonth = $month + 1;
            $nextyear = $year;
        endif;
        if ($month == 1) :
            $previousmonth = 12;
            $previousyear = $year - 1;
        else :
            $previousmonth = $month - 1;
            $previousyear = $year;
        endif;
        
        $days = Array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
        echo '<h3>'.$firstDayArray['month'].' '.$firstDayArray['year'].'</h3>';
        echo '<table class="calendar"><tr>';
        foreach ($days as $day) {
	        echo '<th>'.$day.'</th>';
        }
        for ($count=0; $count < (6*7); $count++) {
	        $dayArray = getdate($start);
	        if (($count % 7) == 0) {
		        if ($dayArray["mon"] != $month) {
			        break;
		        } else {
			        echo "</tr><tr>";
		        }
	        }
	        if ($count < $firstDayArray["wday"] || $dayArray["mon"] != $month) {
		        echo '<td class="calendar-empty">&nbsp;</td>';
	        } else {
	            // setup database
    	        $registry = Zend_Registry::getInstance();
    	
    	        $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
				   		           ->where('event_status = ?','published')
				   		           ->where('month(event_date) = ?',$month)
				   		           ->where('dayofmonth(event_date) = ?',$dayArray['mday'])
				   		           ->where('year(event_date) = ?',$year)
    					           ->order('e.event_date ASC');
    					           
    		    // Set the data array
		        $eventsArray = $registry->db->fetchall($select);
		        
		        if (count($eventsArray)) :
	                echo '<td class="calendar-event"><a href="'.$this->view->baseUrl().'/events/calendar/year/'.$year.'/month/'.$month.'/day/'.$dayArray['mday'].'/" id="day'.$dayArray['mday'].'">'.$dayArray['mday'].'</a></td>';
	                echo '<div dojoType="dijit.Tooltip" connectId="day'.$dayArray['mday'].'" position="[\'left\']">';
	                Zend_Date::setOptions(array('format_type' => 'php'));
	                foreach ($eventsArray as $event) :
	                    echo $this->view->ECDate($event['event_date']).' <strong>'.$event['event_title'].'</strong><br />';
	                endforeach;
					echo '</div>';
	            else :
	                echo '<td>'.$dayArray['mday'].'</td>';
	            endif;
	            
		        $start += ADAY;
	        }
        }
        echo '</tr></table>';
        echo '<div class="nButWrapL" style="margin-top: 4px; margin-left: 5px;"><a href="'.$this->view->baseUrl().'/events/calendar/year/'.$year.'/month/'.$month.'/">View as list &raquo;</a></div>';
        echo '<div class="nButWrapR" style="margin-top: 5px;">';
        echo '<a href="#" onclick="calGo(\''.$this->view->baseUrl().$previousmonth.'\',\''.$previousyear.'\');return false;">Previous</a>';
        echo '<a href="#" onclick="calGo(\''.$this->view->baseUrl().$nextmonth.'\',\''.$nextyear.'\');return false;">Next</a>';
        echo '</div>';
        echo '<p class="ClearAll"></p>';
    }
    /**
     *  
     */
    public function ECalendar ($month,$year)
    {
        $calendar = $this->generateCalendar($month,$year);
        
        return $calendar;
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
