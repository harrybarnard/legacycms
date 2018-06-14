<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_EventsController extends CMS_Controller_Action_Admin
{
	
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}
	
	/**
     *  
     */
    public function EDate($startdate,$enddate)
    {
        
        $fstart = strtotime($startdate); // convert start date to timestamp
        $start = date('l jS F Y g:ia',$fstart); // create formatted start date from timestamp 
        
        if($enddate != NULL) : // If and end date is set
            $sstart = date('dmY',$fstart); // create start date string for comparison
        
            $fend = strtotime($enddate); // convert end date to timestamp
            $send = date('dmY',$fend); // create end date for comparison
        
            if($sstart == $send) : // If the start and end dates match
                $end = ' - '.date('g:ia',$fend); // create formatted end time
            else :
                $end = ' - '.date('l jS F Y g:ia',$fend); // create formatted end date
            endif;
        
            return $start.$end; // Return both dates
            
        else : // If end date is not set
        
            return $start; // Return start date only
           
        endif;
    	
    }
	
	public function getVenue($venueid)
	{
	    // Get registry
    	$registry = Zend_Registry::getInstance();
    	
	    // Get venue details from db
    	$select = $registry->db->select()
    					       ->from(array('v' => 'events_venues'))
    					       ->where('v.venue_id = ?', $venueid);

		$venueArray = $registry->db->fetchall($select);
		
		// If venue exists
		if (count($venueArray) > 0) :

            $venueArray = $venueArray[0];
            
            // If venue isn't default
            if($venueArray['venue_id'] != '1') :
        
                $venuetitle = $venueArray['venue_title'].' ('.$venueArray['venue_city'].')';
        
                return $this->view->venue = $venuetitle; // Return venue title and city
                
            else :
                
                return $this->view->venue = $venueArray['venue_title']; // Return venue title
                
            endif;
            
        else :
        
            return false;
            
        endif;
	}
	
    public function getCategory($categoryid)
	{
	    // Get registry
    	$registry = Zend_Registry::getInstance();
    	
	    // Get category details from db
    	$select = $registry->db->select()
    					       ->from(array('c' => 'events_categories'))
    					       ->where('c.ecat_id = ?', $categoryid);

		$categoryArray = $registry->db->fetchall($select);
		
		// If category exists
		if (count($categoryArray) > 0) :

            $categoryArray = $categoryArray[0];
        
            $categoryname = $categoryArray['ecat_title'];
        
            return $this->view->category = $categoryname; // Return category title
           
        else :
        
            return false;
            
        endif;
	}
	
	/**
	 * The default action
	 */
	public function indexAction() {
		$this->_helper->redirector('manage','events','admin'); // Redirect to manage action
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents')) :
	    
		    // setup database
    	    $registry = Zend_Registry::getInstance();
		
		    $this->setLayout(); // Set layout
		
		    // Set input variables
		    $category = $this->_request->getParam('category');
		    $venue = $this->_request->getParam('venue');
		
	        // Set page number
		    $page = $this->_getParam('page');
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
    	
    	    if ($category != NULL & $venue != NULL) : // If category and venue are set
    	        $this->getVenue($venue); // Get venue name
    	        $this->getCategory($category); // Get category name
    	        $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->where('e.event_category = ?', $category)
    					           ->where('e.event_venue = ?', $venue)
				   		           ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.venue_id','v.venue_title','v.venue_city'))
    					           ->order('e.event_date DESC');
    	    elseif ($category != NULL) : // If category is set
    	        $this->getCategory($category); // Get category name
    	        $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->where('e.event_category = ?', $category)
				   		           ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.venue_id','v.venue_title','v.venue_city'))
    					           ->order('e.event_date DESC');
            elseif ($venue != NULL) : // If venue is set
                $this->getVenue($venue); // Get venue name
                $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->where('e.event_venue = ?', $venue)
				   		           ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.venue_id','v.venue_title','v.venue_city'))
    					           ->order('e.event_date DESC');
    	    
    	    else : // If no input vars are set
    	        $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
				   		           ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.venue_id','v.venue_title','v.venue_city'))
    					           ->order('e.event_date DESC');
    	    endif;

    	    // Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(15);
		    $paginator->setPageRange(5);
		
		    // Save Paginator as View var.
		    $this->view->eventsArray = $paginator;
		    
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Delete action -  Delete event
	 */
	public function deleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventdelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            // Set input variables
	        $this->view->confirm = $this->_getParam('confirm');
            $this->view->event = $this->_getParam('id');
        
            // If input confirmed and event is set
            if ($this->view->confirm == '1' & isset($this->view->event)) :
        
                // Get registry
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Delete the event
		        $registry->db->delete('events', 'event_id = '.$this->view->event);
		    
                // Delete associated comments
		        $registry->db->delete('comments', array('comment_type = "E"','comment_slave = '.$this->view->event));
		    
		        // Remove from search index
		        $this->_helper->searchIndex->delete('e'.$this->view->event);
            
		        // Output success response
		        echo '<p class="Spacer"></p>
					<div class="cUpd">Event Deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventdeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		    
	        else :
	        
	            // Output confirmation response
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to delete this event?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventdeleteButton" value="Delete" iconClass="deleteeventIcon" onClick="getDialog(\'/admin/events/delete/id/'.$this->view->event.'/confirm/1/\',\'Delete Event\');">Delete</button>
						<button dojoType="dijit.form.Button" type="button" id="eventdeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
					</div>';
	        
	        endif;
	        
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
	 * Save action - Save event
	 */
	public function saveAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventedit')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
		    // Set input variables
		    $id = $this->_getParam('id');
        
            if (isset($id)) :
        
                // Filter comments checkbox values    
                if ($this->_getParam('comments') != 'Y') :
                    $comments = 'N';
                else :
                    $comments = 'Y';
                endif;
            
                // Filter moderate checkbox values   
                if ($this->_getParam('moderate') != 'Y') :
                    $moderate = 'N';
                else :
                    $moderate = 'Y';
                endif;
            
	            $options = array();

                $filters = array();

                $validators = array(
                	'title' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
            		'category' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    ),
                	'venue' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Venue is required"))
                    ),
                	'datestart' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Start Date is required"))
                    ),
                	'timestart' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Start Time is required"))
                    ),
                	'dateend' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "End Date is required"))
                    ),
                	'timeend' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "End Time is required"))
                    ),
                	'dateend' => array('allowEmpty' => true),
                	'timeend' => array('allowEmpty' => true),
                	'description' => array(
                		'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'url' => array('allowEmpty' => true),
                	'ticket' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                // Get registry
 	            $registry = Zend_Registry::getInstance();

                // If input is valid
 	            if ($input->isValid()) :
            
 	                $formatstart = date('Y-m-d H:i:s', strtotime($input->datestart.' '.$input->timestart)); // Format event start date
                    
                    $formatend = date('Y-m-d H:i:s', strtotime($input->dateend.' '.$input->timeend)); // Format event end date
                    
                    // Create input data array
                    $data = array(
                		'event_title'	    => $input->title,
                		'event_category'	=> $input->category,
                    	'event_venue'	    => $input->venue,
                    	'event_date'		=> $formatstart,
                    	'event_end'		    => $formatend,
                    	'event_description'	=> html_entity_decode($input->description),
                    	'event_user'	    => $this->view->user->user_id,
                    	'event_url'	   		=> $input->url,
                    	'event_tickets'	    => $input->ticket,
                    	'event_comments'	=> $comments,
                    	'event_moderate'	=> $moderate
                   );

                   // Update data in database
                   $registry->db->update('events', $data, 'event_id = '.$id);
               
                   // Get events details from db
    	           $select = $registry->db->select()
    	                                  ->from(array('e' => 'events'))
    	                                  ->where('e.event_id = ?', $id)
    	                                  ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.venue_id','v.venue_title','v.venue_address','v.venue_country','v.venue_city'))
    	                                  ->limit(1,0);

		           $eventArray = $registry->db->fetchall($select);

                   $event = $eventArray['0'];
               
                   // If event is published
                   if ($event['event_status'] == 'published') :
                
                       // Create search index url 
                       $docUrl = '/events/event/'.$id.'/'.urlencode($input->title).'/';
                    
                       // If event venue isn't default 
                       if($event['venue_id'] != '1') :
                
                           // Create search index details field 
                           $details = $this->EDate($formatstart,$formatend).', '.$event['venue_title'].', '.$event['venue_address'].', '.$event['venue_city'];
                        
                       else :
                    
                           // Create search index details field 
                           $details = $this->EDate($formatstart,$formatend);
                        
                       endif;
                
                       $this->_helper->searchIndex->update(array('key' => 'e'.$id,
                       										  'date' => $this->_helper->makeDate('Ymd',$event['event_published']),
                                                              'title' => $input->title,
                                                              'url' => $docUrl,
                                                              'details' => $details,
                                                              'stub' => html_entity_decode($input->description),
                                                              'contents' => html_entity_decode($input->description).html_entity_decode($input->description)
                                                           ));
                       
                   endif;
               
                   // Output success response
                   echo '<p class="Spacer"></p>
					<div class="cUpd">Event Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="eventsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                        
                endif;
		    
	        else :
	        
	            // Output event not specified response    
	            echo '<p class="Spacer"></p>
					<div class="cErr">Event Not Specified!</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Publish action
	 */
	public function publishAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventpublish')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
	    
		    $this->view->confirm = $this->_getParam('confirm');
            $this->view->event = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->event)) :
        
	            // Filter comments checkbox values    
                if ($this->_getParam('comments') != 'Y') :
                    $comments = 'N';
                else :
                    $comments = 'Y';
                endif;
            
                // Filter moderate checkbox values   
                if ($this->_getParam('moderate') != 'Y') :
                    $moderate = 'N';
                else :
                    $moderate = 'Y';
                endif;
            
	            $options = array(
	             	'notEmptyMessage' => "A non-empty value is required for field '%field%'" 
                );

                $filters = array();

                $validators = array(
                	'title' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
            		'category' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    ),
                	'venue' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Venue is required"))
                    ),
                	'datestart' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Start Date is required"))
                    ),
                	'timestart' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Start Time is required"))
                    ),
                	'dateend' => array('allowEmpty' => true),
                	'timeend' => array('allowEmpty' => true),
                	'description' => array(
                		'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'url' => array('allowEmpty' => true),
                	'ticket' => array('allowEmpty' => true)
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
    	        $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    $formatstart = date('Y-m-d H:i:s', strtotime($input->datestart.' '.$input->timestart));
                    
                    $formatend = date('Y-m-d H:i:s', strtotime($input->dateend.' '.$input->timeend));
                    
                    // Create our data array
                    $data = array(
                		'event_title'	    => $input->title,
                		'event_category'	=> $input->category,
                    	'event_venue'	    => $input->venue,
                    	'event_date'		=> $formatstart,
                    	'event_end'		    => $formatend,
                    	'event_description'	=> html_entity_decode($input->description),
                    	'event_user'	    => $this->view->user->user_id,
                    	'event_url'	   		=> $input->url,
                    	'event_tickets'	    => $input->ticket,
                    	'event_comments'	=> $comments,
                    	'event_moderate'	=> $moderate,
                    	'event_published'	=> new Zend_Db_Expr('NOW()'),
                    	'event_status'		=> 'published'
                   );

                   // Insert data into database
                   $registry->db->update('events', $data, 'event_id = '.$this->view->event);
               
                   // Build the query
    	           $select = $registry->db->select()
    	                                  ->from(array('e' => 'events'))
    	                                  ->where('e.event_id = ?', $this->view->event)
    	                                  ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.venue_id','v.venue_title','v.venue_address','v.venue_country','v.venue_city'))
    	                                  ->limit(1,0);

		           // Set the data array
		           $eventArray = $registry->db->fetchall($select);

                   $event = $eventArray['0'];
               
                   $docUrl = '/events/event/'.$this->view->event.'/'.urlencode($input->title).'/';
                    
                   if($event['venue_id'] != '1') :
                
                        $details = $this->EDate($formatstart,$formatend).', '.$event['venue_title'].', '.$event['venue_address'].', '.$event['venue_city'];
                        
                   else :
                    
                        $details = $this->EDate($formatstart,$formatend);
                        
                   endif;
                   
                   $this->_helper->searchIndex->add(array('key' => 'e'.$this->view->event,
                       									  'date' => $this->_helper->makeDate('Ymd'),
                                                          'title' => $input->title,
                                                          'url' => $docUrl,
                                                          'details' => $details,
                                                          'stub' => html_entity_decode($input->description),
                                                          'contents' => html_entity_decode($input->description).html_entity_decode($input->description)
                                                       ));
               
                   echo '<p class="Spacer"></p>
					<div class="cUpd">Event Published</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventpublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventpublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to publish this event?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="eventpublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/events/publish/id/'.$this->view->event.'/confirm/1/\',\'editForm\',\'Publish Event\',\'1\');">Publish</button>
					<button dojoType="dijit.form.Button" type="button" id="eventpublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Edit action
	 */
	public function editAction() 
	{
		if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventedit')) :
		
	        $this->setLayout();
		
		    $eventid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('e' => 'events'))
    	                           ->where('e.event_id = ?', $eventid)
    	                           ->join(array('u' => 'users'),'e.event_user = u.user_id',array('u.user_alias'))
    	                           ->limit(1,0);

		    // Set the data array
		    $eventArray = $registry->db->fetchall($select);

            $this->view->eventArray = $eventArray['0'];
        
            if(!count($eventArray)) :
                $this->_helper->redirector('manage','events','admin');
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Details action - display event details
	 */
	public function detailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $eventid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('e' => 'events'))
    	                           ->where('e.event_id = ?', $eventid)
    	                           ->join(array('u' => 'users'),'e.event_user = u.user_id',array('u.user_alias'))
    	                           ->limit(1,0);

		    // Set the data array
		    $eventArray = $registry->db->fetchall($select);

            $this->view->eventArray = $eventArray['0'];
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * New action - create a new event
	 */
	public function newAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventnew')) :
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'eventvenue' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Venue is required"))
                    ),
            		'eventcategory' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
                
                    $this->_redirector = $this->_helper->getHelper('Redirector');
                    $this->_redirector->gotoSimple('new2',
                                                   'events',
                                                   'admin',
                                                   array('venue' => $input->eventvenue,
                                                         'category' => $input->eventcategory
                                                        )
                                                  );

                else :
            
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->eventvenue = $input->eventvenue;
                    $this->view->eventcategory = $input->eventcategory;
                
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
    /**
	 * New action - create a new event
	 */
	public function new2Action() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'eeventnew')) :
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	        
	        $this->view->category = $this->_getParam('category');
	        
	        $this->view->venue = $this->_getParam('venue');

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'eventtitle' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Name is required"))
                    ),
                    'eventvenue' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Venue is required"))
                    ),
                    'eventcategory' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    ),
                	'eventdatestart' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Start Date is required"))
                    ),
                	'eventtimestart' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Start Time is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	
    	            // Build the query
    	            $select = $registry->db->select()
    	                               ->from(array('c' => 'events_categories'))
    	                               ->where('c.ecat_id = ?', $input->eventcategory)
    	                               ->limit(1,0);

		            // Set the data array
		            $categoryArray = $registry->db->fetchall($select);

                    $this->view->categoryArray = $categoryArray['0'];
                
                    $formatstart = date('Y-m-d H:i:s', strtotime($input->eventdatestart.' '.$input->eventtimestart));
                
                    $formatend = date('Y-m-d H:i:s', strtotime($formatstart.' + 2 hours'));
                    
                    if($input->eventvenue == 0) :
                        $venue = $this->view->categoryArray['ecat_venue'];
                    else :
                        $venue = $input->eventvenue;
                    endif;
                        
                    // Create our data array
                    $data = array(
                		'event_title'	    => $input->eventtitle,
                		'event_category'	=> $input->eventcategory,
                    	'event_venue'		=> $venue,
                    	'event_description'	=> $this->view->categoryArray['ecat_default'],
                    	'event_date'		=> $formatstart,
                		'event_end'		    => $formatend,
                    	'event_user'	    => $this->view->user->user_id
                    );

                    // Insert data into database
                    $registry->db->insert('events', $data);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Event Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="eventnewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/events/edit/id/'.$registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                
                 else :
            
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->eventtitle = $input->eventtitle;
                    $this->view->eventstartdate = $input->eventstartdate;
                
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
     * Categories Action - Render a list of categories
     */
    public function categoriesAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents')) :
        
            $this->_helper->layout->disableLayout(); // Disable Layouts
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('c' => 'events_categories'))
    	                           ->order('c.ecat_title ASC');

		    // Set the data array
		    $this->view->categoryArray = $registry->db->fetchall($select);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * Category action
	 */
	public function categoryAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'ecategoryedit')) :
		    
	        $this->setLayout();
		
		    $catid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('c' => 'events_categories'))
    	                           ->where('c.ecat_id = ?', $catid)
    	                           ->limit(1,0);

		    // Set the data array
		    $categoryArray = $registry->db->fetchall($select);

            $this->view->categoryArray = $categoryArray['0'];
        
            if(!count($categoryArray)) :
                $this->_helper->redirector('manage','events','admin');
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Category Details action - display events category details
	 */
	public function categorydetailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'ecategoryedit')) :
	        
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $catid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('c' => 'events_categories'))
    	                           ->where('c.ecat_id = ?', $catid)
    	                           ->limit(1,0);

		    // Set the data array
		    $categoryArray = $registry->db->fetchall($select);

            $this->view->categoryArray = $categoryArray['0'];
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
    
	/**
     * Delete Category Action - Remove an event category and move associated articles to category default
     */
    public function categorydeleteAction () 
    { 
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'ecategorydelete')) :
         
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
            
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->category = $this->_getParam('id');
        
            // If delete confirmed category is set and category isn't default
            if ($this->view->confirm == '1' & isset($this->view->category) & $this->view->category != '1') :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Query for articles with this category
    	        $select = $registry->db->select()
    	                               ->from(array('e' => 'events'))
    	                               ->where('e.event_category = ?', $this->view->category);

		        // Set the array of events
		        $eventArray = $registry->db->fetchall($select);
		    
		        // Move each event in category to default category
		        foreach($eventArray as $event) :
    	    
    	            // Create our data array
                    $data = array(
                		'event_category' => '1'
                    );

                    // Insert data into database
                    $registry->db->update('events', $data, 'event_id = '.$event['event_id']);
                
                endforeach;
    	
    	        // Delete the article
		        $registry->db->delete('events_categories', 'ecat_id = '.$this->view->category);
            
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Category Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="categorydeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to delete this category?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorydeleteButton" value="Delete" iconClass="deletecategoryIcon" onClick="getDialog(\'/admin/events/categorydelete/id/'.$this->view->category.'/confirm/1/\',\'Delete Category\');">Delete</button>
						<button dojoType="dijit.form.Button" type="button" id="categorydeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * Add New action - create a new category
	 */
	public function categorynewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'ecategorynew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'categorytitle' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists('events_categories','ecat_title'),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Category Already Exists"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	
                    // Create our data array
                    $data = array(
                		'ecat_title'	    => $input->categorytitle,
                		'ecat_venue'	    => '1'
                    );

                    // Insert data into database
                    $registry->db->insert('events_categories', $data);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Category Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorynewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/events/category/id/'.$registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                
                 else :
             
                    $this->view->posted = 'N';
            
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->categorytitle = $this->_getParam('categorytitle');
                
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
     * Save Category Action - Upadte an event category
     */
    public function categorysaveAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'ecategoryedit')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->category = $this->_getParam('id');
        
            if (isset($this->view->category)) :
        
	            $options = array();

                $filters = array();
    
                $validators = array(
                	'title' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'description' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Teaser is required"))
                    ),
                	'content' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'venue' => array('allowEmpty' => true),
                	'default' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'ecat_title'	    => $input->title,
                    	'ecat_description'	=> html_entity_decode($input->description),
                		'ecat_content'	    => html_entity_decode($input->content),
                		'ecat_venue'	    => $input->venue,
                		'ecat_default'	    => html_entity_decode($input->default)
                    );

                    // Insert data into database
                    $registry->db->update('events_categories', $data, 'ecat_id = '.$this->view->category);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Category Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorysavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorysavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cErr">Category Not Specified!</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="categorysavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Publish Category Action - Update an event category
     */
    public function categorypublishAction () 
    {  
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'ecategorypublish')) :
         
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->category = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->category)) :
        
	            $options = array(
	        		'notEmptyMessage' => "A non-empty value is required for field '%field%'" 
                );

                $filters = array();

                $validators = array(
                	'title' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'description' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'content' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Content is required"))
                    ),
                	'venue' => array('allowEmpty' => true),
                	'default' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'ecat_title'	    => $input->title,
                    	'ecat_description'	=> html_entity_decode($input->description),
                		'ecat_content'	    => html_entity_decode($input->content),
                		'ecat_venue'	    => $input->venue,
                		'ecat_default'	    => html_entity_decode($input->default),
                		'ecat_status'	    => 'published'
                    );

                    // Insert data into database
                    $registry->db->update('events_categories', $data, 'ecat_id = '.$this->view->category);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Category Published</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorypublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorypublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to publish this category?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="categorypublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/events/categorypublish/id/'.$this->view->category.'/confirm/1/\',\'editForm\',\'Publish Category\',\'1\');">Publish</button>
					<button dojoType="dijit.form.Button" type="button" id="categorypublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * Venues Manage action
	 */
	public function venuesAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents')) :
	    
		    // setup database
    	    $registry = Zend_Registry::getInstance();
		
		    $this->setLayout();
		
		    $this->view->city = $city = urldecode($this->_request->getParam('city'));
		
		    $this->view->country = $country = urldecode($this->_request->getParam('country'));
		
	        $page = $this->_getParam('page');
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
    	
    	    if ($city != NULL & $country != NULL) :
    	            $select = $registry->db->select()
    					           ->from(array('v' => 'events_venues'))
    					           ->where('v.venue_id != ?', '1')
    					           ->where('v.venue_city = ?', $city)
    					           ->where('v.venue_country = ?', $country)
    					           ->order('v.venue_title ASC');
    	    elseif ($city != NULL) :
    	        $select = $registry->db->select()
    					           ->from(array('v' => 'events_venues'))
    					           ->where('v.venue_id != ?', '1')
    					           ->where('v.venue_city = ?', $city)
    					           ->order('v.venue_title ASC');
            elseif ($country != NULL) :
                $select = $registry->db->select()
    					           ->from(array('v' => 'events_venues'))
    					           ->where('v.venue_id != ?', '1')
    					           ->where('v.venue_country = ?', $country)
    					           ->order('v.venue_title ASC');
    	    else :
    	        $select = $registry->db->select()
    					           ->from(array('v' => 'events_venues'))
    					           ->where('v.venue_id != ?', '1')
    					           ->order('v.venue_title ASC');
    	    endif;

    	    //Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(15);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->venuesArray = $paginator;
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Venue action
	 */
	public function venueAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'evenueedit')) :
	    
		    $this->setLayout();
		
		    $venueid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('v' => 'events_venues'))
    	                           ->where('v.venue_id = ?', $venueid)
    	                           ->limit(1,0);

		    // Set the data array
		    $venueArray = $registry->db->fetchall($select);

            $this->view->venueArray = $venueArray['0'];
        
            if(!count($venueArray)) :
                $this->_helper->redirector('venues','events','admin');
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Venue Details action - display events venue details
	 */
	public function venuedetailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'evenueedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $venueid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('v' => 'events_venues'))
    	                           ->where('v.venue_id = ?', $venueid)
    	                           ->limit(1,0);

		    // Set the data array
		    $venueArray = $registry->db->fetchall($select);

            $this->view->venueArray = $venueArray['0'];
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
     * Delete Venue Action - Remove a venue and move associated events to default venue
     */
    public function venuedeleteAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'evenuedelete')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->venue = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->venue)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Query for articles with this venue
    	        $select = $registry->db->select()
    	                               ->from(array('e' => 'events'))
    	                               ->where('e.event_venue = ?', $this->view->venue);

		        // Set the array of articles
		        $eventArray = $registry->db->fetchall($select);
		    
		        foreach($eventArray as $event) :
    	    
    	            // Create our data array
                    $data = array(
                		'event_venue' => '1'
                    );

                    // Insert data into database
                    $registry->db->update('events', $data, 'event_id = '.$event['event_id']);
                
                endforeach;
    	
    	        // Delete the article
		        $registry->db->delete('events_venues', 'venue_id = '.$this->view->venue);
		    
		        $this->_helper->searchIndex->delete('ev'.$this->view->venue);
            
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Venue Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="venuedeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            // Output confirmation response    
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this venue?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="venuedeleteButton" value="Delete" iconClass="deletevenueIcon" onClick="getDialog(\'/admin/events/venuedelete/id/'.$this->view->venue.'/confirm/1/\',\'Delete Venue\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="venuedeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * New Venue - create a new venue
	 */
	public function venuenewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'evenuenew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        // If input is posted
	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'venuetitle' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'venueaddress' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Address is required"))
                    ),
                	'venuecity' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "City is required"))
                    ),
                	'venuecountry' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Country is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // If input is valid
                if ($input->isValid()) :
            
    	            // Get registry    
                    $registry = Zend_Registry::getInstance();
    	        
    	            // Get Google Maps API key
    	            $apikey = $registry->site->key->gmap;
   
                    // Create geocode query string
    	            $geoquery = $input->venueaddress.'+'.$input->venuecity.'+'.$input->venuecountry;

                    // Geocode Address
                    $url = "http://maps.google.com/maps/geo?q=".$geoquery."&output=xml&key=".$apikey;

                    // Load Geocode response
                    $kml = simplexml_load_file($url);
               
                    // Get coordinates from response
                    $coords = $kml->Response->Placemark->Point->coordinates;
                
                    // Seperate and set coords
                    list($longitude,$latitude,$altitude) = explode(",",$coords);
    	
                    // Create input data array
                    $data = array(
                		'venue_title'	    => $input->venuetitle,
                		'venue_address'	    => $input->venueaddress,
                    	'venue_city'	    => $input->venuecity,
                    	'venue_country'	    => $input->venuecountry,
                    	'venue_latitude'	=> $latitude,
                    	'venue_longitude'	=> $longitude
                    );

                    // Insert data into database
                    $registry->db->insert('events_venues', $data);
                    
                    // Output success response
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Venue Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="venuenewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/events/venue/id/'.$registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                
                 else :
            
                    $this->view->posted = 'N';
            
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->venuetitle = $input->venuetitle;
                    $this->view->venueaddress = $input->venueaddress;
                    $this->view->venuecity = $input->venuecity;
                    $this->view->venuecountry = $input->venuecountry;
                        
                endif;
             
             else :
         
                 // Set form as NOT posted    
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
     * Save Venue Action - Update a venue
     */
    public function venuesaveAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'evenueedit')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            // Set input variables
            $this->view->venue = $this->_getParam('id');
        
            // If venue is specified
            if (isset($this->view->venue)) :
        
	            $options = array();

                $filters = array();

                $validators = array(
                	'title' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'description' => array('allowEmpty' => true),
                	'latitude' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Latitude is required"))
                    ),
                	'longitude' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Longitude is required"))
                    ),
                	'address' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Address is required"))
                    ),
                	'city' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "City is required"))
                    ),
                	'country' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Country is required"))
                    ),
                	'url' => array('allowEmpty' => true),
                	'email' => array('allowEmpty' => true),
                	'phone' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // Get registry
  	            $registry = Zend_Registry::getInstance();

                // If input is valid
  	            if ($input->isValid()) :
            
                    // Create input data array
                    $data = array(
                		'venue_title'	    => $input->title,
                    	'venue_description'	=> html_entity_decode($input->description),
                		'venue_latitude'	=> $input->latitude,
                		'venue_longitude'	=> $input->longitude,
                    	'venue_address'	    => $input->address,
                    	'venue_city'	    => $input->city,
                    	'venue_country'	    => $input->country,
                		'venue_url'	        => $input->url,
                    	'venue_email'	    => $input->email,
                    	'venue_phone'	    => $input->phone
                    );

                    // Insert data into database
                    $registry->db->update('events_venues', $data, 'venue_id = '.$this->view->venue);
                
                    // Get venue details from db
    	            $select = $registry->db->select()
    	                               ->from(array('v' => 'events_venues'))
    	                               ->where('v.venue_id = ?', $this->view->venue)
    	                               ->limit(1,0);

		            $venueArray = $registry->db->fetchall($select);

                    $venue = $venueArray['0'];
                
                    // If venue is published
                    if ($venue['venue_status'] == 'published') :
                
                        // Create search index url field  
                        $docUrl = '/events/venue/'.$this->view->venue.'/'.urlencode($input->title).'/';
                
                        // Create search index details field
                        $details = $input->address.', '.$input->city.', '.$input->country;
                        
                        $this->_helper->searchIndex->update(array('key' => 'ev'.$this->view->venue,
                       									  	      'date' => $this->_helper->makeDate('Ymd'.$venue['venue_published']),
                                                          		  'title' => $input->title,
                                                          		  'url' => $docUrl,
                                                          		  'details' => $details,
                                                          		  'stub' => html_entity_decode($input->description),
                                                              	  'contents' => html_entity_decode($input->description).html_entity_decode($input->description)
                                                               ));
                
                    endif;

                    // Output success response
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Venue Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="venuesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="venuesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            // Output venue not set error response    
	            echo '<p class="Spacer"></p>
				<div class="cErr">Venue Not Specified!</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="venuesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Publish Venue Action - Publish a venue
     */
    public function venuepublishAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'eevents') & $this->view->acl->isAllowed($this->view->user->user_role, 'evenuepublish')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            // Set input variables
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->venue = $this->_getParam('id');
        
            // If input confirmed and venue set
            if ($this->view->confirm == '1' & isset($this->view->venue)) :
        
	            $options = array();

                $filters = array();

                $validators = array(
                	'title' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'description' => array('allowEmpty' => true),
                	'latitude' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Latitude is required"))
                    ),
                	'longitude' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Longitude is required"))
                    ),
                	'address' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Address is required"))
                    ),
                	'city' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "City is required"))
                    ),
                	'country' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Country is required"))
                    ),
                	'url' => array('allowEmpty' => true),
                	'email' => array('allowEmpty' => true),
                	'phone' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // Get registry
  	            $registry = Zend_Registry::getInstance();

                // If input is valid
  	            if ($input->isValid()) :
            
                    // Create input data array
                    $data = array(
                		'venue_title'	    => $input->title,
                    	'venue_description'	=> html_entity_decode($input->description),
                		'venue_latitude'	=> $input->latitude,
                		'venue_longitude'	=> $input->longitude,
                    	'venue_address'	    => $input->address,
                    	'venue_city'	    => $input->city,
                    	'venue_country'	    => $input->country,
                		'venue_url'	        => $input->url,
                    	'venue_email'	    => $input->email,
                    	'venue_phone'	    => $input->phone,
                    	'venue_published'	=> new Zend_Db_Expr('NOW()'),
                    	'venue_status'		=> 'published'
                    );

                    // Insert data into database
                    $registry->db->update('events_venues', $data, 'venue_id = '.$this->view->venue);
                
                    // Create search index url field
                    $docUrl = '/events/venue/'.$this->view->venue.'/'.urlencode($input->title).'/';
                
                    // Create search index details field
                    $details = $input->address.', '.$input->city.', '.$input->country;
                    
                    $this->_helper->searchIndex->add(array('key' => 'ev'.$this->view->venue,
                       									   'date' => $this->_helper->makeDate('Ymd'),
                                                           'title' => $input->title,
                                                           'url' => $docUrl,
                                                           'details' => $details,
                                                           'stub' => html_entity_decode($input->description),
                                                           'contents' => html_entity_decode($input->description).html_entity_decode($input->description)
                                                         ));
                                                               
                    // Output success response
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Venue Published</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="venuepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="venuepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            // Output confirmation request    
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to publish this venue?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="venuepublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/events/venuepublish/id/'.$this->view->venue.'/confirm/1/\',\'editForm\',\'Publish Venue\',\'1\');">Publish</button>
						<button dojoType="dijit.form.Button" type="button" id="venuepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
}