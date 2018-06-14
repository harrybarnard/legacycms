<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_ResourcesController extends CMS_Controller_Action_Admin
{
	
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}
	
	public function getType($type)
	{
    	$registry = Zend_Registry::getInstance();
    	
        $select = $registry->db->select()
    					       ->from(array('t' => 'resources_types'))
    					       ->where('t.rtype_id = ?', $type);
    					       
        $tArray = $registry->db->fetchall($select);
		
        $typeArray = $tArray[0];
        
        return $this->view->type = $typeArray['rtype_plural'];
	}
	
    public function getCategory($categoryid)
	{
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
	    $select = $registry->db->select()
    					       ->from(array('c' => 'resources_categories'))
    					       ->where('c.rcat_id = ?', $categoryid);

		$categoryArray = $registry->db->fetchall($select);
		
		if (count($categoryArray) > 0) :

            $categoryArray = $categoryArray[0];
        
            $categoryname = $categoryArray['rcat_title'];
        
            return $this->view->category = $categoryname;
           
        else :
        
            return false;
            
        endif;
	}
	
	/**
	 * The default action
	 */
	public function indexAction() 
	{
		$this->_helper->redirector('manage','resources','admin');
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources')) :
	    
		    // setup database
    	    $registry = Zend_Registry::getInstance();
		
		    $this->setLayout();
		
		    $category = $this->_request->getParam('category');
		
		    $type = $this->_request->getParam('type');
		
	        $page = $this->_getParam('page');
	        
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
    	
    	    if ($category != NULL & $type != NULL) :
    	        $this->getType($type);
    	        $this->getCategory($category);
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
    					           ->where('r.resource_category = ?', $category)
    					           ->where('r.resource_type = ?', $type)
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
    					           ->order('r.resource_date DESC');
    	    elseif ($category != NULL) :
    	        $this->getCategory($category);
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
    					           ->where('r.resource_category = ?', $category)
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
    					           ->order('r.resource_date DESC');
            elseif ($type != NULL) :
                $this->getType($type);
                $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
    					           ->where('r.resource_type = ?', $type)
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
    					           ->order('r.resource_date DESC');
    	    else :
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
    					           ->order('r.resource_date DESC');
    	    endif;

    	    //Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(15);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->resourcesArray = $paginator;
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Edit action
	 */
	public function editAction() 
	{
		if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourceedit')) :
		
	        $this->setLayout();
		
		    $id = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('r' => 'resources'))
    	                       ->where('r.resource_id = ?', $id)
    	                       ->limit(1,0);

		    // Set the data array
		    $resourceArray = $registry->db->fetchall($select);

            $this->view->resourceArray = $resourceArray['0'];
        
            if(!count($resourceArray)) :
                $this->_helper->redirector('manage','resources','admin');
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Delete action
	 */
	public function deleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourcedelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
            
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->resource = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->resource)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Delete the article
		        $registry->db->delete('resources', 'resource_id = '.$this->view->resource);
		    
		        // Delete associated tags
		        $registry->db->delete('tags', array('tag_type = "R"','tag_slave = '.$this->view->resource));
		    
                // Delete associated comments
		        $registry->db->delete('comments', array('comment_type = "R"','comment_slave = '.$this->view->resource));
		        
		        // Remove from search index
		        $this->_helper->searchIndex->delete('r'.$this->view->resource);
            
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Resource Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="resourcedeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this resource?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="resourcedeleteButton" value="Delete" iconClass="deleteresourceIcon" onClick="getDialog(\'/admin/resources/delete/id/'.$this->view->resource.'/confirm/1/\',\'Delete Resource\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="resourcedeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
	 * Save action
	 */
	public function saveAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourceedit')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
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
                
                // setup database
    	        $registry = Zend_Registry::getInstance();
                
                // Get events details from db
    	        $select = $registry->db->select()
    	                           ->from(array('r' => 'resources'))
    	                           ->where('r.resource_id = ?', $id)
    	                           ->limit(1,0);

		        $resourceArray = $registry->db->fetchall($select);

                $resource = $resourceArray['0'];
            
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
                    'brand' => array(
                        'presence' => 'required',
                        'NotEmpty',
                     	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Brand is required"))
                    ),
                    'author' => array('allowEmpty' => true),
                    'illustrator' => array('allowEmpty' => true),
                    'isbn' => array('allowEmpty' => true),
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
                    'asset' => array('allowEmpty' => true),
                    'comments' => array('allowEmpty' => true),
                    'moderate' => array('allowEmpty' => true)
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                // setup database
 	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
                
                    if($resource['resource_type'] == '1') :
            
                        // Create our data array
                        $data = array(
                			'resource_title'	    => $input->title,
                    		'resource_brand'	    => $input->brand,
                			'resource_category'	    => $input->category,
                    		'resource_author'	    => $input->author,
                    		'resource_illustrator'  => $input->illustrator,
                        	'resource_ISBN'         => $input->isbn,
                    		'resource_description'	=> $input->description,
                    		'resource_content'	    => html_entity_decode($input->content),
                    		'resource_asset'	    => $input->asset,
                    		'resource_comments'	    => $comments,
                    		'resource_moderate'	    => $moderate,
                    		'resource_edit'		    => new Zend_Db_Expr('NOW()')
                       );
                   
                   else :
                   
                       // Create our data array
                       $data = array(
                		   'resource_title'	        => $input->title,
                    	   'resource_brand'	        => $input->brand,
                		   'resource_category'	    => $input->category,
                    	   'resource_author'	    => NULL,
                    	   'resource_illustrator'   => NULL,
                           'resource_ISBN'          => NULL,
                    	   'resource_description'	=> $input->description,
                    	   'resource_content'	    => html_entity_decode($input->content),
                    	   'resource_asset'	        => $input->asset,
                    	   'resource_comments'	    => $comments,
                    	   'resource_moderate'	    => $moderate,
                    	   'resource_edit'		    => new Zend_Db_Expr('NOW()')
                       );
                       
                   endif;
                   
                   // Insert data into database
                   $registry->db->update('resources', $data, 'resource_id = '.$id);

                   // If event is published
                   if (count($resourceArray) & $resource['resource_status'] == 'published') :
                   
                       // Create search index url 
                       $docUrl = '/resources/resource/'.$id.'/'.urlencode($input->title).'/';
                    
                       // Create search index details field 
                       $details = NULL;
                        
                       $this->_helper->searchIndex->update(array('key' => 'r'.$id,
                       										 	 'date' => $this->_helper->makeDate('Ymd',$resource['resource_published']),
                                                             	 'title' => $input->title,
                                                             	 'url' => $docUrl,
                                                             	 'details' => $details,
                                                             	 'stub' => html_entity_decode($input->description),
                                                             	 'contents' => html_entity_decode($input->content)
                                                               ));
                    
                   endif;
                    
                   echo '<p class="Spacer"></p>
					<div class="cUpd">Resource Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="resourcesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="resourcesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cErr">Resource Not Specified!</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="resourcesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourcepublish')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
	    
		    $this->view->confirm = $this->_getParam('confirm');
            $this->view->resource = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->resource)) :
            
                // setup database
    	        $registry = Zend_Registry::getInstance();
            
                // Get events details from db
    	        $select = $registry->db->select()
    	                           ->from(array('r' => 'resources'))
    	                           ->where('r.resource_id = ?', $this->view->resource)
    	                           ->limit(1,0);

		        $resourceArray = $registry->db->fetchall($select);

                $resource = $resourceArray['0'];
        
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
                    'brand' => array(
                        'presence' => 'required',
                        'NotEmpty',
                     	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Brand is required"))
                    ),
                    'author' => array('allowEmpty' => true),
                    'illustrator' => array('allowEmpty' => true),
                    'isbn' => array('allowEmpty' => true),
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
                    'asset' => array('allowEmpty' => true),
                    'comments' => array('allowEmpty' => true),
                    'moderate' => array('allowEmpty' => true)
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
    	        $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    if($resource['resource_type'] == '1') :
            
                        // Create our data array
                        $data = array(
                			'resource_title'	    => $input->title,
                    		'resource_brand'	    => $input->brand,
                			'resource_category'	    => $input->category,
                    		'resource_author'	    => $input->author,
                    		'resource_illustrator'  => $input->illustrator,
                        	'resource_ISBN'         => $input->isbn,
                    		'resource_description'	=> $input->description,
                    		'resource_content'	    => html_entity_decode($input->content),
                    		'resource_asset'	    => $input->asset,
                    		'resource_comments'	    => $comments,
                    		'resource_moderate'	    => $moderate,
                    		'resource_edit'		    => new Zend_Db_Expr('NOW()'),
                        	'resource_publish'	    => new Zend_Db_Expr('NOW()'),
                            'resource_status'		=> 'published'
                       );
                   
                   else :
                   
                       // Create our data array
                       $data = array(
                		   'resource_title'	        => $input->title,
                    	   'resource_brand'	        => $input->brand,
                		   'resource_category'	    => $input->category,
                    	   'resource_author'	    => NULL,
                    	   'resource_illustrator'   => NULL,
                           'resource_ISBN'          => NULL,
                    	   'resource_description'	=> $input->description,
                    	   'resource_content'	    => html_entity_decode($input->content),
                    	   'resource_asset'	        => $input->asset,
                    	   'resource_comments'	    => $comments,
                    	   'resource_moderate'	    => $moderate,
                    	   'resource_edit'		    => new Zend_Db_Expr('NOW()'),
                       	   'resource_publish'	    => new Zend_Db_Expr('NOW()'),
                           'resource_status'		=> 'published'
                       );
                       
                   endif;

                   // Insert data into database
                   $registry->db->update('resources', $data, 'resource_id = '.$this->view->resource);
                   
                   // Create search index url 
                   $docUrl = '/resources/resource/'.$this->view->resource.'/'.urlencode($input->title).'/';
                    
                   // Create search index details field 
                   $details = NULL;
                        
                   $this->_helper->searchIndex->add(array('key' => 'r'.$this->view->resource,
                       										 'date' => $this->_helper->makeDate('Ymd'),
                                                             'title' => $input->title,
                                                             'url' => $docUrl,
                                                             'details' => $details,
                                                             'stub' => $input->description,
                                                             'contents' => html_entity_decode($input->content)
                                                           ));
                   
                   echo '<p class="Spacer"></p>
				   <div class="cUpd">Resource Published</div>
				   <p class="Spacer"></p>
				   <div class="cFormDS">
					   <button dojoType="dijit.form.Button" type="button" id="resourcepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				   </div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="resourcepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to publish this resource?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="resourceepublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/resources/publish/id/'.$this->view->resource.'/confirm/1/\',\'editForm\',\'Publish Resource\',\'1\');">Publish</button>
					<button dojoType="dijit.form.Button" type="button" id="resourcepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Details action - display article details
	 */
	public function detailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourceedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $resourceid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('r' => 'resources'))
    	                       ->where('r.resource_id = ?', $resourceid)
    	                       ->limit(1,0);

		    // Set the data array
		    $resourceArray = $registry->db->fetchall($select);

            $this->view->resourceArray = $resourceArray[0];
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * New action - create a new article
	 */
	public function newAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourcenew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                    'resourcetitle' => array(
                        'presence' => 'required',
                        'NotEmpty',
                        'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                    'resourcecategory' => array(
                        'presence' => 'required',
                        'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    ),
                    'resourcetype' => array(
                        'presence' => 'required',
                        'NotEmpty',
                     	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Type is required"))
                    ),
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	
                    // Create our data array
                    $data = array(
                		'resource_title'	    => $input->resourcetitle,
                    	'resource_type'	        => $input->resourcetype,
                        'resource_brand'	    => '1',
                		'resource_category'	    => $input->resourcecategory,
                    	'resource_user'	        => $this->view->user->user_id,
                    	'resource_edit'		    => new Zend_Db_Expr('NOW()'),
                    );

                    // Insert data into database
                    $registry->db->insert('resources', $data);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Resource Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="resourcenewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/resources/edit/id/'.$registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                
                 else :
            
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->resourcetitle = $input->resourcetitle;
                    $this->view->resourcecategory = $input->resourcecategory;
                    $this->view->resourcetype = $input->resourcetype;
             
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
     * Delete Category Action - Remove a resource category and move associated resources to category default
     */
    public function categorydeleteAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourcedelete')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->category = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->category)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Query for articles with this category
    	        $select = $registry->db->select()
    	                               ->from(array('r' => 'resources'))
    	                               ->where('r.resource_category = ?', $this->view->category);

		        // Set the array of articles
		        $resourceArray = $registry->db->fetchall($select);
		    
		        foreach($resourceArray as $resource) :
    	    
    	            // Create our data array
                    $data = array(
                		'resource_category' => '1'
                    );

                    // Insert data into database
                    $registry->db->update('resources', $data, 'resource_id = '.$resource['resource_id']);
                
                endforeach;
    	
    	        // Delete the article
		        $registry->db->delete('resources_categories', 'rcat_id = '.$this->view->category);
            
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
					<button dojoType="dijit.form.Button" type="button" id="categorydeleteButton" value="Delete" iconClass="deletecategoryIcon" onClick="getDialog(\'/admin/resources/categorydelete/id/'.$this->view->category.'/confirm/1/\',\'Delete Category\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="categorydeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * New category action - create a new category
	 */
	public function categorynewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rresourcenew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'categorytitle' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists('resources_categories','rcat_title'),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Category Already Exists"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'rcat_title'    => $input->categorytitle
                    );
                    
                    // Setup Registry
    	            $registry = Zend_Registry::getInstance();

                    // Insert data into database
                    $registry->db->insert('resources_categories', $data);
                    
                    echo '<p class="Spacer"></p>
						<div class="cUpd">Category Created</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="categorynewcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
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
	 * Category action
	 */
	public function categoryAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rcategoryedit')) :
		    
	        $this->setLayout();
		
		    $catid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('c' => 'resources_categories'))
    	                           ->where('c.rcat_id = ?', $catid)
    	                           ->limit(1,0);

		    // Set the data array
		    $categoryArray = $registry->db->fetchall($select);

            $this->view->categoryArray = $categoryArray['0'];
        
            if(!count($categoryArray)) :
                $this->_helper->redirector('manage','resources','admin');
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rcategoryedit')) :
	        
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $catid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('c' => 'resources_categories'))
    	                           ->where('c.rcat_id = ?', $catid)
    	                           ->limit(1,0);

		    // Set the data array
		    $categoryArray = $registry->db->fetchall($select);

            $this->view->categoryArray = $categoryArray['0'];
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
    
	
	/**
     * Save Category Action - Upadte an event category
     */
    public function categorysaveAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rcategoryedit')) :
        
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
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'content' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Content is required"))
                    ),
                	'asset' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'rcat_title'	    => $input->title,
                    	'rcat_description'	=> $input->description,
                		'rcat_content'	    => html_entity_decode($input->content),
                		'rcat_asset'	    => $input->asset
                    );

                    // Insert data into database
                    $registry->db->update('resources_categories', $data, 'rcat_id = '.$this->view->category);
                    
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
        if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rcategorypublish')) :
         
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
                	'asset' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'rcat_title'	    => $input->title,
                    	'rcat_description'	=> $input->description,
                		'rcat_content'	    => html_entity_decode($input->content),
                		'rcat_asset'	    => $input->asset,
                        'rcat_status'		=> 'published'
                    );

                    // Insert data into database
                    $registry->db->update('resources_categories', $data, 'rcat_id = '.$this->view->category);
                    
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
					<button dojoType="dijit.form.Button" type="button" id="categorypublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/resources/categorypublish/id/'.$this->view->category.'/confirm/1/\',\'editForm\',\'Publish Category\',\'1\');">Publish</button>
					<button dojoType="dijit.form.Button" type="button" id="categorypublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
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
        if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources')) :
        
            $this->_helper->layout->disableLayout(); // Disable Layouts
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('c' => 'resources_categories'))
    	                       ->order('c.rcat_title ASC');

		    // Set the data array
		    $this->view->categoryArray = $registry->db->fetchall($select);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * Brands Manage action
	 */
	public function brandsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rbrands')) :
	    
		    // setup database
    	    $registry = Zend_Registry::getInstance();
		
		    $this->setLayout();
		    
	        $page = $this->_getParam('page');
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
    	
    	    $select = $registry->db->select()
    					       ->from(array('b' => 'resources_brands'))
    					       ->where('b.rbrand_id != ?', '1')
    					       ->order('b.rbrand_title ASC');

    	    //Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(15);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->brandsArray = $paginator;
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * New brand action - create a new brand
	 */
	public function brandnewAction() {
	    
	    $this->_helper->layout->disableLayout(); // Disable Layouts

	    if($this->_request->isPost()) :
                
	        $options = array();

            $filters = array(
                'brandphone' => array('StringTrim')
            );

            $validators = array(
                'brandtitle' => array(
                	'presence' => 'required',
            		'NotEmpty',
                    new Zend_Validate_Db_NoRecordExists('resources_brands','rbrand_title'),
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Duplicate Title")),
                    'breakChainOnFailure' => true
                ),
                'brandlink' => array(
                	'allowEmpty' => true,
                    new Zend_Validate_Regex('((mailto\:|(news|(ht|f)tp(s?))\://){1}\S+)'),
                    'messages' => array(array( Zend_Validate_Regex::NOT_MATCH => "Invalid phone number")),
                ),
                'brandphone' => array(
                	'allowEmpty' => true,
                	new Zend_Validate_Regex('/^[a-zA-Z-\' ]*$/'),
                    'messages' => array(array( Zend_Validate_Regex::NOT_MATCH => "Invalid phone number")),
                )
            );

            $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
            if ($input->isValid()) :
            
                // setup database
    	        $registry = Zend_Registry::getInstance();
            
                // Create our data array
                $data = array(
                	'rbrand_title'    => $input->brandtitle,
                    'rbrand_link'     => $input->brandlink,
                    'rbrand_phone'    => $input->brandphone
                );

                // Insert data into database
                $registry->db->insert('resources_brands', $data);
                    
                echo '<p class="Spacer"></p>
					<div class="cUpd">Brand Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="brandnewcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
                    
             else :
             
                $this->view->posted = 'N';
                
                // Set Error Messages
                $this->view->messages = $input->getMessages();
                
                $this->view->brandtitle = $input->brandtitle;
                $this->view->brandlink = $input->brandlink;
                $this->view->brandphone = $input->brandphone;
            
            endif;
             
         else :
         
             $this->view->posted = 'N';
         
         endif;
	    
	}
	
	/**
	 * Brand Delete action
	 */
	public function branddeleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rbranddelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
            
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->brand = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->brand)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Delete the article
		        $registry->db->delete('resources_brands', 'rbrand_id = '.$this->view->brand);
		    
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Brand Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="branddeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this brand?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="branddeleteButton" value="Delete" iconClass="deletebrandIcon" onClick="getDialog(\'/admin/resources/branddelete/id/'.$this->view->brand.'/confirm/1/\',\'Delete Brand\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="branddeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
	 * Brand action - edit category
	 */
	public function brandAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rresources') & $this->view->acl->isAllowed($this->view->user->user_role, 'rbrandedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
	    
	        $registry = Zend_Registry::getInstance();
	    
	        // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('b' => 'resources_brands'))
    	                           ->where('b.rbrand_id = ?', $id)
    	                           ->limit(1,0);

		    // Set the data array
		    $brandArray = $registry->db->fetchall($select);

            $this->view->brandArray = $brandArray['0'];
        
	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'brandtitle' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'resources_brands','field' => 'rbrand_title','exclude' => array('field' => 'rbrand_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Duplicate Title"))
                    ),
                	'brandlink' => array(
                		'allowEmpty' => true,
                        new Zend_Validate_Regex('((mailto\:|(news|(ht|f)tp(s?))\://){1}\S+)'),
                    	'messages' => array(array( Zend_Validate_Regex::NOT_MATCH => "Invalid URL")),
                    ),
                	'brandphone' => array(
                		'allowEmpty' => true,
                	    new Zend_Validate_Regex('/^[0-9 ]*$/'),
                    	'messages' => array(array( Zend_Validate_Regex::NOT_MATCH => "Invalid phone number")),
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'rbrand_title' => $input->brandtitle,
                        'rbrand_link' => $input->brandlink,
                    	'rbrand_phone' => $input->brandphone
                    );

                    // Insert data into database
                    $registry->db->update('resources_brands', $data, 'rbrand_id = '.$id);
                    
                    echo '<p class="Spacer"></p>
						<div class="cUpd">Brand Saved</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="brandeditcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
                    
                 else :
             
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->brandtitle = $input->brandtitle;
                    $this->view->brandlink = $input->brandlink;
                    $this->view->brandphone = $input->brandphone;
            
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	    
	}
    
}