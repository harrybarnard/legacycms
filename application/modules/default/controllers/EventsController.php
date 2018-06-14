<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class EventsController extends CMS_Controller_Action_Default
{
    
    public function init()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load events configuration
        $events = new Zend_Config_Ini('../application/configs/events.ini', array('config'));
        $registry->set('events', $events);
        
        // Change to pages layout
    	$this->_helper->layout->setLayout('events');
    }

	/**
     * The default action - outreach index page
     */
    public function indexAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
        
        // setup database
    	$registry = Zend_Registry::getInstance();
		
    	$select = $registry->db->select()
    					       ->from(array('e' => 'events'))
    					       ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		   ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		       ->where('event_status = ?','published')
				   		       ->where('event_date >= NOW()')
    					       ->order('e.event_date ASC');
    					           
    	//Create Paginator Object
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->view->page);
		$paginator->setItemCountPerPage($registry->events->rows);
		$paginator->setPageRange(5);
		
		//Save Paginator as View var.
		$this->view->eventsArray = $paginator;
    }
    
	/**
	 * Calendar action
	 */
	public function calendarAction() {
	    
		// setup database
    	$registry = Zend_Registry::getInstance();
		
		$this->setRefer();
		
		$year = $this->_request->getParam('year');
		
		$month = $this->_request->getParam('month');
		
		$day = $this->_request->getParam('day');
		
	    $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if ($year != NULL & $month != NULL & $day != NULL) :
    	    $start = mktime (12, 0, 0, $month, $day, $year);
    	    $dateArray = getdate($start);
            $this->view->year = $dateArray['year'];
            $this->view->month = $dateArray['month'];
            $this->view->day = $dateArray['mday'];
    	    $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		       ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->where('event_status = ?','published')
				   		           ->where('month(event_date) = ?',$month)
				   		           ->where('dayofmonth(event_date) = ?',$day)
				   		           ->where('year(event_date) = ?',$year)
    					           ->order('e.event_date ASC');
    					           
    		//Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->events->rows);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->eventsArray = $paginator;
    	elseif ($year != NULL & $month != NULL) :
    	    $start = mktime (12, 0, 0, $month, 1, $year);
    	    $dateArray = getdate($start);
            $this->view->year = $dateArray['year'];
            $this->view->month = $dateArray['month'];
    	    $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		       ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->where('event_status = ?','published')
				   		           ->where('month(event_date) = ?',$month)
				   		           ->where('year(event_date) = ?',$year)
    					           ->order('e.event_date ASC');
    					           
    		//Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->events->rows);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->eventsArray = $paginator;
        elseif ($year != NULL) :
            $start = mktime (12, 0, 0, 1, 1, $year);
            $dateArray = getdate($start);
            $this->view->year = $dateArray['year'];
    	    $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		       ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->where('event_status = ?','published')
				   		           ->where('year(event_date) = ?',$year)
    					           ->order('e.event_date ASC');
    					           
    	    //Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->events->rows);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->eventsArray = $paginator;
    	else :
    	    $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
    	endif;
	}
	
	/**
	 * Event action
	 */
	public function eventAction() {
	    
		// setup database
    	$registry = Zend_Registry::getInstance();
		
		$this->setRefer();
		
		$eventid = $this->_request->getParam('id');
		
    	if (isset($eventid)) : // If Venue ID set continue
    	
    	    $select = $registry->db->select()
    					           ->from(array('e' => 'events'))
    					           ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		       ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		           ->where('event_status = ?','published')
				   		           ->where('event_id = ?',$eventid)
    					           ->limit(1,0);

    	    // Set the data array
		    $eventArray = $registry->db->fetchall($select);
		    
		    $this->view->eventArray = $eventArray[0];
		    
		    $this->view->comment = NULL; // Reset the comment value
        
            if($this->_request->isPost()) // If form has been posted
            {
                if ($this->view->authenticated) // The user is authenticated
                {
                    $options = array();

                    $filters = array(
            			'content' => array('StringTrim', 'StripTags')
                    );

                    $validators = array(
                        'content' => array(
                    		'presence' => 'required',
                    		'NotEmpty',
                 			'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "You did not enter a comment"))
                        ),
                    );

                    $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                    // setup database
    	            $registry = Zend_Registry::getInstance();

                    if ($input->isValid()) // If the form input validates
                    {
                        // Set moderation variable
                        if($this->view->articleArray['article_moderate'] == 'Y') :
                            $approved = 'N';
                        else :
                            $approved = 'Y';
                        endif;
                    
                        // Create our data array
                        $data = array(
                        	'comment_type'      => 'E',
    						'comment_slave'     => $this->view->eventArray['event_id'],
    						'comment_approved'  => $approved,
                			'comment_content'	=> $input->content,
                			'comment_user'	    => $this->view->user->user_id,
                			'comment_date'		=> new Zend_Db_Expr('NOW()')
                        );

                        // Insert data into database
                        $registry->db->insert('comments', $data);
                
                    } else { // If input is invalid
                        $this->view->messages = $input->getMessages(); // Pass Messages to view
                        $this->view->comment = $_POST['content']; // Set value of message to match input
                    }
                }
            }
        
            // Get Comments
    	    $select = $registry->db->select()
    					       ->from(array('c' => 'comments'),array('c.*'))
				   		       ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role'))
				   		       ->where('comment_approved = ?','Y')
				   		       ->where('comment_slave = ?',$eventid)
    					       ->order('c.comment_date ASC');

    	    // Pass the comments to view
		    $this->view->commentsArray = $registry->db->fetchall($select);
		    
    	else :
    	    $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
    	endif;

	}
	
	/**
	 * Category action
	 */
	public function categoryAction() {
	    
		// setup database
    	$registry = Zend_Registry::getInstance();
		
		$this->setRefer();
		
		$cattitle = urldecode($this->_request->getParam('category'));
		
	    $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if (isset($cattitle)) : // If Venue ID set continue
    	
    	    // Get Venue data
    	    $select = $registry->db->select()
    					       ->from(array('c' => 'events_categories'))
				   		       ->where('ecat_title = ?',$cattitle)
    					       ->limit(1,0);

    	    // Set the data array
		    $categoryArray = $registry->db->fetchall($select);
		    
		    if (count($categoryArray)) : // If Article exists

                // Pass Venue to View    
		        $this->view->categoryArray = $categoryArray[0];
		        
		        $categoryid = $this->view->categoryArray['ecat_id'];
		        
		        $eselect = $registry->db->select()
    					               ->from(array('e' => 'events'))
    					               ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		           ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		               ->where('event_status = ?','published')
				   		               ->where('event_date >= NOW()')
				   		               ->where('event_category = ?',$categoryid)
    					               ->order('e.event_date ASC');
    					           
    		    //Create Paginator Object
		        $paginator = Zend_Paginator::factory($eselect);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->events->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->eventsArray = $paginator;
            
            else : // Else redirect to 404 Error
        
    	        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
            
            endif;
    	
    	else :
    	    $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
    	endif;
	}
	
	/**
	 * Venue action
	 */
	public function venueAction() 
	{
	    
		// setup database
    	$registry = Zend_Registry::getInstance();
		
		$this->setRefer();
		
		$venueid = $this->_request->getParam('id');
		
	    $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if (isset($venueid)) : // If Venue ID set continue
    	
    	    // Get Venue data
    	    $select = $registry->db->select()
    					       ->from(array('v' => 'events_venues'))
				   		       ->where('venue_id = ?',$venueid)
    					       ->limit(1,0);

    	    // Set the data array
		    $venueArray = $registry->db->fetchall($select);
		    
		    if (count($venueArray) && $venueid != '1') : // If Venue exists and/or isn't default venue

                // Pass Venue to View    
		        $this->view->venueArray = $venueArray[0];
		        
		        $eselect = $registry->db->select()
    					               ->from(array('e' => 'events'))
    					               ->join(array('v' => 'events_venues'),'e.event_venue = v.venue_id',array('v.*'))
			    	   		           ->join(array('c' => 'events_categories'),'e.event_category = c.ecat_id',array('c.ecat_title'))
				   		               ->where('event_status = ?','published')
				   		               ->where('event_date >= NOW()')
				   		               ->where('event_venue = ?',$venueid)
    					               ->order('e.event_date ASC');
    					           
    		    //Create Paginator Object
		        $paginator = Zend_Paginator::factory($eselect);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->events->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->eventsArray = $paginator;
            
            else : // Else redirect to 404 Error
        
    	        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
            
            endif;
    	
    	else :
    	    $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
    	endif;
	}
    
	/**
     * Map Action - display outreach map
     */
    public function mapAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
    }
    
	/**
     * AJAX - Calendar Action - display outreach calendar
     */
    public function acalendarAction ()
    {    
        $this->_helper->layout->disableLayout(); // Disable Layouts
        
        $this->view->month = $this->_request->getParam('month');
        $this->view->year = $this->_request->getParam('year');
        
    }
    
	/**
     * Map Data - output data for outreach map
     */
    public function mapdataAction ()
    {    
        $this->_helper->layout->disableLayout(); // Disable Layouts
        
        $this->view->params = $this->_request->getParams();
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    	                       ->from(array('v' => 'events_venues'))
    	                       ->where('venue_id != ?','1');

		// Set the data array
		$this->view->venuesArray = $registry->db->fetchall($select);
    }
    
}