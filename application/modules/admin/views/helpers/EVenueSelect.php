<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * CatSelect helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_EVenueSelect
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function EVenueSelect ($venueid)
    {
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    					           ->from(array('v' => 'events_venues'))
    					           ->where('v.venue_id != ?', '1')
    					           ->where('v.venue_status = ?', 'published')
    					           ->order('v.venue_title ASC');

		// Set the data array
		$venueArray = $registry->db->fetchall($select);
		
		foreach($venueArray as $venue) :
		    if($venueid == $venue['venue_id']) :
		        if($venue['venue_id'] != '1') :
			        echo '<option value="'.$venue['venue_id'].'" selected="selected">'.$venue['venue_title'].' ('.$venue['venue_city'].')</option>';
			    else :
			        echo '<option value="'.$venue['venue_id'].'" selected="selected">'.$venue['venue_title'].'</option>';
			    endif;
			else: 
			    if($venue['venue_id'] != '1') :
			        echo '<option value="'.$venue['venue_id'].'">'.$venue['venue_title'].' ('.$venue['venue_city'].')</option>';
			    else :
			        echo '<option value="'.$venue['venue_id'].'">'.$venue['venue_title'].'</option>';
			    endif;
			endif;
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
