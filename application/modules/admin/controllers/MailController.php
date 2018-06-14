<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_MailController extends CMS_Controller_Action_Admin 
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
		$this->_helper->redirector('manage','mail','admin');
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail')) :
		
    	    $this->_request->setParam('items',15);
		    $this->_request->setParam('range',5);
	    
	        $params = $this->_request->getParams();
		
            $mail = new Mail();
            
            $this->view->filter = $mail->getFilter($params);
		    $this->view->mailArray = $mail->fetchMails($params);
		    $this->view->groupArray = $mail->fetchGroups();
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Save action
	 */
	public function saveAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmailedit')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
		    $id = $this->_getParam('id');
		    
            if (isset($id)) :
        
	            $options = array();

                $filters = array(
                    'slave' => array(
                        'StringTrim',
                        'Int',
                        new Zend_Filter_Null(array('integer'))
                    ),
                    'subject' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'text' => array(
                        'StringTrim',
                        'StripTags',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'html' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    )
                );

                $validators = array(
                	'slave' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "To is required"))
                    ),
            		'subject' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Subject is required"))
                    ),
                	'text' => array('allowEmpty' => true),
                	'html' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $mailArray = $mail->fetchMail($id);
                
                    if($mailArray['mail_status'] != 'sent') :
                        
                        $mail->updateMail(array('id' => $id,
                    							'slave' => $input->slave,
                                         		'subject' => $input->subject,
                                         		'text' => $input->text,
                                         		'html' => html_entity_decode($input->html)
                                                ));
            
                        echo '<p class="Spacer"></p>
						<div class="cUpd">Mail Saved</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="mailsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                       
                    else :
                    
                        echo '<p class="Spacer"></p>
	            		<div class="cErr">Mail Already Sent!</div>
	            		<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="mailsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                         
                    endif;
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                        <div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="mailsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                    endif;
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
	            	<div class="cErr">Mail Not Specified!</div>
	            	<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="mailsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
	        
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmaildelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
            
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
            
                $mail = new Mail();
                $mail->deleteMail($id);
        
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Mail Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="maildeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this mail?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="maildeleteButton" value="Delete" iconClass="deletemailIcon" onClick="getDialog(\'/admin/mail/delete/id/'.$id.'/confirm/1/\',\'Delete Mail\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="maildeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uview')) :
	    
		    $id = $this->_getParam('id');
		    
		    $mail = new Mail();
            $this->view->mailArray = $mail->fetchMail($id);
		
            if(!count($this->view->mailArray)) :
                $this->_helper->redirector('manage','mail','admin');
            endif;
            
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Details action - display user details
	 */
	public function detailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uview')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
	        
	        $mail = new Mail();
	        $this->view->mailArray = $mail->fetchMail($id);
		
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
    public function maillistAction()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmailnew')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
	        
            if($this->_request->isPost()) :
        
	            $options = array();

                $filters = array(
                    'slave' => array(
                        'StringTrim',
                        'Int',
                        new Zend_Filter_Null(array('integer'))
                    ),
                    'subject' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                );
            
                $validators = array(
                	'slave' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "To is required")),
                    	'breakChainOnFailure' => true
                    ),
            		'subject' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Subject is required")),
                    	'breakChainOnFailure' => true
                    ),
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $groupArray = $mail->fetchGroup($input->slave);
                    $mail->newMail(array('type' => 'G',
                                         'slave' => $input->slave,
                                         'subject' => $input->subject,
                                         'text' => $groupArray['mgroup_text'],
                                         'html' => $groupArray['mgroup_html'],
                                         'author' => $this->view->user->user_id
                                        ));
            
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Mail Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="mailnewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/mail/edit/id/'.$this->registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                    
                 else :
                
                     $this->view->posted = 'N';
                 
                     // Pass input variables to populate form
                     $this->view->slave = $this->_request->getParam('slave');
                     $this->view->subject = $this->_request->getParam('subject');
                 
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
    
    public function mailroleAction()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmailnew')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
	        
            if($this->_request->isPost()) :
        
	            $options = array();

                $filters = array(
                    'slave' => array(
                        'StringTrim',
                        'Int',
                        new Zend_Filter_Null(array('integer'))
                    ),
                    'subject' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                );
            
                $validators = array(
                	'slave' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "To is required")),
                    	'breakChainOnFailure' => true
                    ),
            		'subject' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Subject is required")),
                    	'breakChainOnFailure' => true
                    ),
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $mail->newMail(array('type' => 'R',
                                         'slave' => $input->slave,
                                         'subject' => $input->subject,
                                         'text' => NULL,
                                         'html' => NULL,
                                         'author' => $this->view->user->user_id
                                        ));
            
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Mail Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="mailnewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/mail/edit/id/'.$this->registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                    
                 else :
                
                     $this->view->posted = 'N';
                 
                     // Pass input variables to populate form
                     $this->view->slave = $this->_request->getParam('slave');
                     $this->view->subject = $this->_request->getParam('subject');
                 
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
    
    public function mailuserAction()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmailnew')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
	        
            // Setup Registry
    	    $this->registry = Zend_Registry::getInstance();
    	    
    	    $this->view->userid = $this->_request->getParam('id');
    	    
            if($this->_request->isPost()) :
        
	            $options = array();

                $filters = array(
                    'subject' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                );
            
                $validators = array(
            		'subject' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Subject is required")),
                    	'breakChainOnFailure' => true
                    ),
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
                
                    $mail = new Mail();
                    //$groupArray = $mail->fetchGroup($input->slave);
                    $mail->newMail(array('type' => 'U',
                                         'slave' => $this->view->userid,
                                         'subject' => $input->subject,
                                         'text' => NULL,
                                         'html' => NULL,
                                         'author' => $this->view->user->user_id
                                        ));
            
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Mail Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="mailnewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/mail/edit/id/'.$this->registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                    
                 else :
                
                     $this->view->posted = 'N';
                 
                     // Pass input variables to populate form
                     $this->view->slave = $this->_request->getParam('slave');
                     $this->view->subject = $this->_request->getParam('subject');
                 
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
     * Send Test Mail
     */
    public function sendtestAction () 
    {  
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmailsend')) :
         
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
            
                $options = array();
        
                $filters = array(
                    'slave' => array(
                        'StringTrim',
                        'Int',
                        new Zend_Filter_Null(array('integer'))
                    ),
                    'subject' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'text' => array(
                        'StringTrim',
                        'StripTags',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'html' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    )
                );

                $validators = array(
                	'slave' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "To is required"))
                    ),
            		'subject' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Subject is required"))
                    ),
                	'text' => array('allowEmpty' => true),
                	'html' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $mailArray = $mail->fetchMail($id);
                    $attachments = new Attachments();
                    $attachArray = $attachments->fetchAttachments(array('type' => 'M',
                                                                        'slave' => $id
                                                                       ));
                
		            $syspath = $this->registry->assets->assets->syspath;

                    if($mailArray['mail_status'] != 'sent') :
                    
                        $mail->updateMail(array('id' => $id,
                    							'slave' => $input->slave,
                                         		'subject' => $input->subject,
                                         		'text' => $input->text,
                                         		'html' => html_entity_decode($input->html)
                                                ));
                                                
                       $mailArray = $mail->fetchMail($id);
            
                       if($mailArray['mail_text'] != NULL) :
                       
                           $textmail = new Zend_Mail();
                           $textmail->setBodyText($mailArray['mail_text']);
                           $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                           $textmail->addTo($this->view->user->user_email, $this->view->user->user_alias);
                           $textmail->setSubject($mailArray['mail_subject']);
                           
                           foreach ($attachArray as $attachment) :
                           
                                $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                $attach = $textmail->createAttachment($fileContents);
                                $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                           endforeach;
                           
                           $textmail->send($this->registry->mail);
                           
                       endif;
                           
                       if($mailArray['mail_html'] != NULL) :
                       
                           $htmlmail = new Zend_Mail();
                           $htmlmail->setBodyHtml($mailArray['mail_html']);
                           $htmlmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                           $htmlmail->addTo($this->view->user->user_email, $this->view->user->user_alias);
                           $htmlmail->setSubject($mailArray['mail_subject']);
                           
                           foreach ($attachArray as $attachment) :
                           
                                $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                $attach = $htmlmail->createAttachment($fileContents);
                                $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension'];
                                
                           endforeach;
                           
                           $htmlmail->send($this->registry->mail);
                           
                       endif;
                       
                       if($mailArray['mail_html'] == NULL & $mailArray['mail_text'] == NULL) :
                       
                           $blankmail = new Zend_Mail();
                           $blankmail->setBodyText($mailArray['mail_text']);
                           $blankmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                           $blankmail->addTo($this->view->user->user_email, $this->view->user->user_alias);
                           $blankmail->setSubject($mailArray['mail_subject']);
                           
                           foreach ($attachArray as $attachment) :
                           
                                $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                $attach = $blankmail->createAttachment($fileContents);
                                $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension'];
                                
                           endforeach;
                           
                           $blankmail->send($this->registry->mail);
                           
                       endif;
               
                       echo '<p class="Spacer"></p>
						<div class="cUpd">Test Mail Sent</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                       
                    else :
                    
                         echo '<p class="Spacer"></p>
	            			<div class="cErr">Mail Already Sent!</div>
	            			<p class="Spacer"></p>
							<div class="cFormDS">
								<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
							</div>';
                         
                    endif;
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">This will send a test mail to: '.$this->view->user->user_email.'</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="mailsendButton" value="Send Test" iconClass="sendmailIcon" onClick="postDialog(\'/admin/mail/sendtest/id/'.$id.'/confirm/1/\',\'editForm\',\'Send Test Mail\',\'1\');">Send Test</button>
					<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Send Mail
     */
    public function sendAction () 
    {  
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mmailsend')) :
         
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
            
                $options = array();
        
                $filters = array(
                    'slave' => array(
                        'StringTrim',
                        'Int',
                        new Zend_Filter_Null(array('integer'))
                    ),
                    'subject' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'text' => array(
                        'StringTrim',
                        'StripTags',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'html' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    )
                );

                $validators = array(
                	'slave' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "To is required"))
                    ),
            		'subject' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Subject is required"))
                    ),
                	'text' => array('allowEmpty' => true),
                	'html' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $mailArray = $mail->fetchMail($id);
                    $attachments = new Attachments();
                    $attachArray = $attachments->fetchAttachments(array('type' => 'M',
                                                                        'slave' => $id
                                                                       ));
                
		            $syspath = $this->registry->assets->assets->syspath;

                    if($mailArray['mail_status'] != 'sent') :
                    
                        $mail->updateMail(array('id' => $id,
                    							'slave' => $input->slave,
                                         		'subject' => $input->subject,
                                         		'text' => $input->text,
                                         		'html' => html_entity_decode($input->html)
                                                ));
                                                
                       $mailArray = $mail->fetchMail($id);
                       
                       if($mailArray['mail_type'] == 'G') :
                       
                           $listArray = $mail->fetchGroup($mailArray['mail_slave']);
                       
                           if($mailArray['mail_text'] != NULL & $mailArray['mail_html'] != NULL) :
                           
                               $textsubsArray = $mail->fetchSubscriptions(array('group' => $mailArray['mail_slave'],
                                                               'format' => 'text'
                                                              ));
                       
                               $textmail = new Zend_Mail();
                               $textmail->setBodyText($mailArray['mail_text']);
                               $textmail->addTo($this->registry->site->site->email, $listArray['mgroup_title']);
                               $textmail->setSubject($mailArray['mail_subject']);
                               $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);

                               foreach ($textsubsArray as $sub) :
                                   
                                   $textmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $textmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $textmail->send($this->registry->mail);
                               
                               $htmlsubsArray = $mail->fetchSubscriptions(array('group' => $mailArray['mail_slave'],
                                                               'format' => 'html'
                                                              ));
		                       
		                       // Send HTML Version
                               $htmlmail = new Zend_Mail();
                               $htmlmail->setBodyHtml($mailArray['mail_html']);
                               $htmlmail->addTo($this->registry->site->site->email, $listArray['mgroup_title']);
                               $htmlmail->setSubject($mailArray['mail_subject']);
                               $htmlmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);

                               foreach ($htmlsubsArray as $sub) :
                                   
                                   $htmlmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $htmlmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $htmlmail->send($this->registry->mail);
                           
                           elseif($mailArray['mail_html'] != NULL) :
                           
                               $subsArray = $mail->fetchSubscriptions(array('group' => $mailArray['mail_slave']));
		                       
                               $bmail = new Zend_Mail();
                               $bmail->setBodyHtml($mailArray['mail_html']);
                               $bmail->addTo($this->registry->site->site->email, $listArray['mgroup_title']);
                               $bmail->setSubject($mailArray['mail_subject']);
                               $bmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($subsArray as $sub) :
                       
                                   $bmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $bmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $bmail->send($this->registry->mail);
                           
                           else :
                       
                               $subsArray = $mail->fetchSubscriptions(array('group' => $mailArray['mail_slave']));
                           
                               $textmail = new Zend_Mail();
                               $textmail->setBodyText($mailArray['mail_text']);
                               $textmail->addTo($this->registry->site->site->email, $listArray['mgroup_title']);
                               $textmail->setSubject($mailArray['mail_subject']);
                               $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($subsArray as $sub) :
                                   
                                   $textmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $textmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $textmail->send($this->registry->mail);
                           
                           endif;
                       
                       elseif($mailArray['mail_type'] == 'R') :
                       
                           if($mailArray['mail_text'] != NULL & $mailArray['mail_html'] != NULL) :
                       
                               $textsubsArray = $mail->fetchUsers(array('role' => $mailArray['mail_slave'],
                                                       'format' => 'text'
                                                      ));

                               $textmail = new Zend_Mail();
                               $textmail->setBodyText($mailArray['mail_text']);
                               $textmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $textmail->setSubject($mailArray['mail_subject']);
                               $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($textsubsArray as $sub) :
                                   
                                   $textmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $textmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $textmail->send($this->registry->mail);
                               
                               $htmlsubsArray = $mail->fetchUsers(array('role' => $mailArray['mail_slave'],
                                                       'format' => 'html'
                                                      ));
                               
                               $htmlmail = new Zend_Mail();
                               $htmlmail->setBodyHtml($mailArray['mail_html']);
                               $htmlmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $htmlmail->setSubject($mailArray['mail_subject']);
                               $htmlmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);

                               foreach ($htmlsubsArray as $sub) :
                                   
                                   $htmlmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $htmltmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $htmlmail->send($this->registry->mail);
                           
                           elseif($mailArray['mail_html'] != NULL) :
                           
                               $subsArray = $mail->fetchUsers(array('role' => $mailArray['mail_slave']));
                           
                               $bmail = new Zend_Mail();
                               $bmail->setBodyHtml($mailArray['mail_html']);
                               $bmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $bmail->setSubject($mailArray['mail_subject']);
                               $bmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($subsArray as $sub) :
                                   
                                   $bmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $bmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $bmail->send($this->registry->mail);
                           
                           else :
                       
                               $subsArray = $mail->fetchUsers(array('role' => $mailArray['mail_slave']));
                           
                               $textmail = new Zend_Mail();
                               $textmail->setBodyText($mailArray['mail_text']);
                               $textmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $textmail->setSubject($mailArray['mail_subject']);
                               $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($subsArray as $sub) :
                                   
                                   $textmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $textmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $textmail->send($this->registry->mail);
                           
                           endif;
                           
                       elseif($mailArray['mail_type'] == 'U') :
                       
                           if($mailArray['mail_text'] != NULL & $mailArray['mail_html'] != NULL) :
                           
                               $textsubsArray = $mail->fetchUsers(array('user' => $mailArray['mail_slave'],
                                                                        'format' => 'text'
                                                                       ));
                       
                               $textmail = new Zend_Mail();
                               $textmail->setBodyText($mailArray['mail_text']);
                               $textmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $textmail->setSubject($mailArray['mail_subject']);
                               $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);

                               foreach ($textsubsArray as $sub) :

                                   $textmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $textmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $textmail->send($this->registry->mail);
                               
                               $htmlsubsArray = $mail->fetchUsers(array('user' => $mailArray['mail_slave'],
                                                                        'format' => 'html'
                                                                       ));
                               
                               $htmlmail = new Zend_Mail();
                               $htmlmail->setBodyHtml($mailArray['mail_html']);
                               $htmlmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $htmlmail->setSubject($mailArray['mail_subject']);
                               $htmlmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);

                               foreach ($htmlsubsArray as $sub) :
                                   
                                   $htmlmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $htmlmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $htmlmail->send($this->registry->mail);
                           
                           elseif($mailArray['mail_html'] != NULL) :
                           
                               $subsArray = $mail->fetchUsers(array('user' => $mailArray['mail_slave']));
                           
                               $bmail = new Zend_Mail();
                               $bmail->setBodyHtml($mailArray['mail_html']);
                               $bmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $bmail->setSubject($mailArray['mail_subject']);
                               $bmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($subsArray as $sub) :
                                   
                                   $bmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $bmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $bmail->send($this->registry->mail);
                           
                           else :
                           
                               $subsArray = $mail->fetchUsers(array('user' => $mailArray['mail_slave']));
                       
                               $textmail = new Zend_Mail();
                               $textmail->setBodyText($mailArray['mail_text']);
                               $textmail->addTo($this->registry->site->site->email, $this->registry->site->site->name);
                               $textmail->setSubject($mailArray['mail_subject']);
                               $textmail->setFrom($this->registry->site->site->email, $this->registry->site->site->name);
                       
                               foreach ($subsArray as $sub) :
                                   
                                   $textmail->addBcc($sub['user_email'], $sub['user_alias']);
                                   
                               endforeach;
                               
                               foreach ($attachArray as $attachment) :
                           
                                    $fileContents = file_get_contents($syspath.$attachment['asset_file']);
                                    $attach = $htmlmail->createAttachment($fileContents);
                                    $attach->filename = $attachment['asset_name'].'.'.$attachment['asset_extension']; 
                                
                               endforeach;
                               
                               $textmail->send($this->registry->mail);
                           
                           endif;
                           
                       endif;
               
                       echo '<p class="Spacer"></p>
						<div class="cUpd">Mail Sent</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                       
                    else :
                    
                         echo '<p class="Spacer"></p>
	            			<div class="cErr">Mail Already Sent!</div>
	            			<p class="Spacer"></p>
							<div class="cFormDS">
								<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
							</div>';
                         
                    endif;
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to send this mail?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="mailsendButton" value="Send" iconClass="sendmailIcon" onClick="postDialog(\'/admin/mail/send/id/'.$id.'/confirm/1/\',\'editForm\',\'Send Mail\',\'1\');">Send</button>
					<button dojoType="dijit.form.Button" type="button" id="mailsendcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * Group action
	 */
	public function groupAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mlistedit')) :
		    
		    $id = $this->_getParam('id');
		    
		    $mail = new Mail();
            $this->view->groupArray = $mail->fetchGroup($id);
        
            if(!count($this->view->groupArray)) :
                $this->_helper->redirector('manage','mail','admin');
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Group Details action
	 */
	public function groupdetailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mlistedit')) :
	        
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
		
		    $mail = new Mail();
            $this->view->groupArray = $mail->fetchGroup($id);
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
     * Save Group Action - Update a mailing list
     */
    public function groupsaveAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mlistedit')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $id = $this->_getParam('id');
        
            if (isset($id)) :
            
	            $options = array();

                $filters = array(
                    'title' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'description' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'open' => array(
                        'StringTrim',
                    	'Alpha',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'default' => array(
                        'StringTrim',
                    	'Alpha',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'text' => array(
                        'StringTrim',
                        'StripTags',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'html' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    )
                );
    
                $validators = array(
                    'title' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'mail_groups','field' => 'mgroup_title','exclude' => array('field' => 'mgroup_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Mailing list already exists"))
                    ),
                	'description' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'open' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Type is required"))
                    ),
                    'default' => array('allowEmpty' => true),
                	'text' => array('allowEmpty' => true),
                	'html' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $this->registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $mail->updateGroup(array('id' => $id,
                                             'title' => $input->title,
                    						 'description' => $input->description,
                							 'text' => $input->text,
                							 'html'	=> html_entity_decode($input->html),
                        					 'open'	=> $input->open,
                							 'default' => $input->default
                                            ));
            
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Mailing List Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="groupsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="groupsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
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
     * Delete Group Action - Remove a mailing list and move its associated mail to default list
     */
    public function groupdeleteAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mlistdelete') & $this->_getParam('id') != '1') :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
            
                if($id != '1') :
        
                    $mail = new Mail();
                    $mail->deleteGroup($id);
            
		            echo '<p class="Spacer"></p>
					<div class="cUpd">Mailing List Deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="groupdeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		        
		        else :
		            
		            echo '<p class="Spacer"></p>
					<div class="cUpd">This mailing list cannot be deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="groupdeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		            
		        endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this mailing list?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="groupdeleteButton" value="Delete" iconClass="deletelistIcon" onClick="getDialog(\'/admin/mail/groupdelete/id/'.$id.'/confirm/1/\',\'Delete Mailing List\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="groupdeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Publish Group Action
     */
    public function grouppublishAction () 
    {  
        if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mlistpublish')) :
         
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
        
	            $options = array();

                $filters = array(
                    'title' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'description' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'open' => array(
                        'StringTrim',
                    	'Alpha',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'default' => array(
                        'StringTrim',
                    	'Alpha',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'text' => array(
                        'StringTrim',
                        'StripTags',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'html' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    )
                );
    
                $validators = array(
                    'title' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'mail_groups','field' => 'mgroup_title','exclude' => array('field' => 'mgroup_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Mailing list already exists"))
                    ),
                	'description' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Description is required"))
                    ),
                	'open' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Type is required"))
                    ),
                    'default' => array('allowEmpty' => true),
                	'text' => array('allowEmpty' => true),
                	'html' => array('allowEmpty' => true)
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $this->registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
                
                    $mail = new Mail();
                    $mail->updateGroupStatus(array('id' => $id,
                                                   'status' => 'published'
                                                  ));
                    $mail->updateGroup(array('id' => $id,
                                             'title' => $input->title,
                    						 'description' => $input->description,
                							 'text' => $input->text,
                							 'html'	=> html_entity_decode($input->html),
                        					 'open'	=> $input->open,
                							 'default' => $input->default
                                            ));
            
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Mailing List Published</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="grouppublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="grouppublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to publish this mailing list?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="grouppublishButton" value="Publish" iconClass="publishIcon" onClick="postDialog(\'/admin/mail/grouppublish/id/'.$id.'/confirm/1/\',\'editForm\',\'Publish Mailing List\',\'1\');">Publish</button>
					<button dojoType="dijit.form.Button" type="button" id="grouppublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * New group action - create a new mailing list
	 */
	public function groupnewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'mmail') & $this->view->acl->isAllowed($this->view->user->user_role, 'mlistnew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array(
                    'grouptitle' => array(
                        'StringTrim',
                        new Zend_Filter_Null(array('string'))
                    ),
                    'groupopen' => array(
                        'StringTrim',
                    	'Alpha',
                        new Zend_Filter_Null(array('string'))
                    )
                );

                $validators = array(
                    'grouptitle' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists('mail_groups','mgroup_title'),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Category Already Exists"))
                    ),
                	'groupopen' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Type is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    $mail = new Mail();
                    $mail->newGroup(array('title' => $input->grouptitle,
                                          'open' => $input->groupopen
                                         ));
                                                             
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Mailing List Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="groupnewcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
                    
                 else :
             
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->grouptitle = $this->_getParam('grouptitle');
                    $this->view->groupopen = $this->_getParam('grouptitle');
            
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	    
	}
    
}