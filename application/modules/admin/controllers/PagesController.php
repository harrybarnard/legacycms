<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_PagesController extends CMS_Controller_Action_Admin 
{
	/**
     * Sets initial vars for controller
     */
    public function init()
    {
    	$this->registry = Zend_Registry::getInstance();
    	$this->_helper->layout->setLayout('admin');	
    }
    
	/**
	 * The default action
	 */
	public function indexAction() {
		$this->_helper->redirector('manage','pages','admin');
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages')) :
	    
	        $this->_request->setParam('items',15);
		    $this->_request->setParam('range',5);
	    
	        $params = $this->_request->getParams();
		
            $pages = new Pages();
            
            $this->view->filter = $pages->getFilter($params);
		    $this->view->pagesArray = $pages->fetchPages($params);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Edit action
	 */
	public function editAction() 
	{
		if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages') & $this->view->acl->isAllowed($this->view->user->user_role, 'ppageedit')) :
		
		    $id = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
            $pages = new Pages();
            $this->view->pageArray = $pages->fetchPage($id);
        
            if(!count($this->view->pageArray)) :
                $this->_helper->redirector('manage','pages','admin');
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages') & $this->view->acl->isAllowed($this->view->user->user_role, 'ppagedelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
        
                $pages = new Pages();
                $pages->deletePage($id);
                           
		        echo '<p class="Spacer"></p>
					<div class="cUpd">Page Deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagedeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to delete this page?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagedeleteButton" value="Delete" iconClass="deletepageIcon" onClick="getDialog(\'/admin/pages/delete/id/'.$id.'/confirm/1/\',\'Delete Page\');">Delete</button>
						<button dojoType="dijit.form.Button" type="button" id="pagedeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages') & $this->view->acl->isAllowed($this->view->user->user_role, 'ppageedit')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
		    $id = $this->_getParam('id');
		
            if (isset($id)) :
            
	            $options = array();

                $filters = array();

                $validators = array(
                	'title' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'content' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Content is required"))
                    ),
                	'slug' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'pages','field' => 'page_slug','exclude' => array('field' => 'page_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "URL is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid URL"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "URL in use"))
                    ),
               		'section' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
                
                if ($input->isValid()) :
                
                    $pages = new Pages();
                    $pages->updatePage(array('id' => $id,
                                             'title' => $input->title,
                                             'content' => $input->content,
                                             'slug' => $input->slug,
                                             'section' => $input->section
                                            ));
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Page Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                    
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="usersavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
            
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cErr">Page Not Specified!</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages') & $this->view->acl->isAllowed($this->view->user->user_role, 'ppagepublish')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
	    
		    $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
        
	            $options = array();

                $filters = array();
    
                $validators = array(
                	'title' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'content' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Content is required"))
                    ),
                	'slug' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'pages','field' => 'page_slug','exclude' => array('field' => 'page_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "URL is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid URL"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "URL in use"))
                    ),
               		'section' => array('allowEmpty' => true)
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
    	        $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    $pages = new Pages();
                    $pages->updatePageStatus(array('id' => $id,
                    							   'status' => 'published'));
                    $pages->updatePage(array('id' => $id,
                                             'title' => $input->title,
                                             'content' => $input->content,
                                             'slug' => $input->slug,
                                             'section' => $input->section
                                            ));
                                                               
                   echo '<p class="Spacer"></p>
					<div class="cUpd">Page Published</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                    
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to publish this page?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagepublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/pages/publish/id/'.$id.'/confirm/1/\',\'editForm\',\'Publish Page\',\'1\');">Publish</button>
						<button dojoType="dijit.form.Button" type="button" id="pagepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Details action - display page details
	 */
	public function detailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages') & $this->view->acl->isAllowed($this->view->user->user_role, 'ppageedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
		
            $pages = new Pages();
            
            $this->view->pageArray = $pages->fetchPage($id);
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Add New action - create a new page
	 */
	public function newAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'ppages') & $this->view->acl->isAllowed($this->view->user->user_role, 'ppagenew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'pagetitle' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    $pages = new Pages();
                    $pages->newPage(array('title' => $input->pagetitle,
                        				  'author' => $this->view->user->user_id
                                         )); 
                                                          
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Page Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="pagenewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/pages/edit/id/'.$this->registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                
                 else :
             
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->pagetitle = $input->pagetitle;
            
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
             
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	    
	}
	
}