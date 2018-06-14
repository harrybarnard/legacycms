<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_RotatorsController extends CMS_Controller_Action_Admin 
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
		$this->_helper->redirector('manage','rotators','admin');
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rotrotators')) :
	    
	        $this->_request->setParam('items',15);
		    $this->_request->setParam('range',5);
	    
	        $params = $this->_request->getParams();
	    
	        $rotators = new Rotators();
		
		    $this->view->rotatorsArray = $rotators->fetchRotators($params);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Edit action
	 */
	public function editAction() 
	{
		if($this->view->acl->isAllowed($this->view->user->user_role, 'rotrotators') & $this->view->acl->isAllowed($this->view->user->user_role, 'rotrotatoredit')) :
		
		    $this->_request->setParam('items',15);
		    $this->_request->setParam('range',5);
	    
	        $params = $this->_request->getParams();
		
		    $id = $this->_getParam('id');
		    
		    $rotators = new Rotators();

            $this->view->rotatorArray = $rotators->fetchRotator($id);
        
            if(!count($this->view->rotatorArray)) :
                $this->_helper->redirector('manage','rotators','admin');
            endif;
            
		    $this->view->slidesArray = $rotators->fetchSlides(array('rotator' => $id));
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * New slide action - create a new slide
	 */
	public function slidenewAction() {
	    
	    $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	    $this->view->rotator = $this->_getParam('rotator');

	    if($this->_request->isPost()) :
                
	        $options = array();

            $filters = array();

            $validators = array(
                'slidetitle' => array(
                	'presence' => 'required',
            		'NotEmpty',
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Title is required")),
                    'breakChainOnFailure' => true
                ),
                'slidedescription' => array('allowEmpty' => true),
                'slidelink' => array(
                	'presence' => 'required',
            		'NotEmpty',
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Link is required")),
                    'breakChainOnFailure' => true
                ),
                'slidepriority' => array(
                	'presence' => 'required',
            		'NotEmpty',
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Priority is required")),
                    'breakChainOnFailure' => true
                ),
                'asset' => array(
                	'presence' => 'required',
            		'NotEmpty',
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Asset is required")),
                    'breakChainOnFailure' => true
                )
            );

            $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
            if ($input->isValid()) :
            
                $rotators = new Rotators();
                $rotators->newSlide(array('title' => $input->slidetitle,
                        			  	  'rotator' => $this->view->rotator,
                                          'description' => $input->slidedescription,
                                          'link' => $input->slidelink,
                                          'priority' => $input->slidepriority,
                                          'asset' => $input->asset
                                          )); 
            
                echo '<p class="Spacer"></p>
					<div class="cUpd">Slide Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="slidenewcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
                    
             else :
             
                $this->view->posted = 'N';
                
                // Set Error Messages
                $this->view->messages = $input->getMessages();
                
                $this->view->slidetitle = $input->slidetitle;
                $this->view->slidedescription = $input->slidedescription;
                $this->view->slidelink = $input->slidelink;
                $this->view->slidepriority = $input->slidepriority;
                $this->view->asset = $input->asset;
            
            endif;
             
         else :
         
             $this->view->posted = 'N';
         
         endif;
	    
	}
	
	/**
	 * Slide action - edit slide
	 */
	public function slideAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rotrotators') & $this->view->acl->isAllowed($this->view->user->user_role, 'rotrotatoredit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
	        
	        $rotators = new Rotators();
	    
            $this->view->slideArray = $rotators->fetchSlide($id);
        
	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'slidetitle' => array(
                		'presence' => 'required',
            			'NotEmpty',
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Title is required")),
                    	'breakChainOnFailure' => true
                    ),
                	'slidedescription' => array('allowEmpty' => true),
                	'slidelink' => array(
                		'presence' => 'required',
            			'NotEmpty',
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Link is required")),
                    	'breakChainOnFailure' => true
                    ),
                	'slidepriority' => array(
                		'presence' => 'required',
            			'NotEmpty',
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Priority is required")),
                    	'breakChainOnFailure' => true
                    ),
                	'asset' => array(
                		'presence' => 'required',
            			'NotEmpty',
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Asset is required")),
                    	'breakChainOnFailure' => true
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :

                    $rotators->updateSlide(array('id' => $id,
                    							 'title' => $input->slidetitle,
                    							 'description' => $input->slidedescription,
                    							 'link' => $input->slidelink,
                    							 'priority' => $input->slidepriority,
                    							 'asset' => $input->asset
                                                 ));

                    echo '<p class="Spacer"></p>
						<div class="cUpd">Slide Saved</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="slideeditcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
                    
                 else :
             
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
            
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	    
	}
	
	/**
	 * Slide Delete action
	 */
	public function slidedeleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'rotrotators') & $this->view->acl->isAllowed($this->view->user->user_role, 'rotrotatoredit')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
            
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
        
                $rotators = new Rotators();
                $rotators->deleteSlide($id);
		    
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Slide Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="slidedeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this slide?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="slidedeleteButton" value="Delete" iconClass="deleteslideIcon" onClick="getDialog(\'/admin/rotators/slidedelete/id/'.$id.'/confirm/1/\',\'Delete Slide\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="slidedeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
}
