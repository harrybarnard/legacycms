<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_SettingsController extends CMS_Controller_Action_Admin
{
    public function init()
    {
    }
    
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}
	
    private function generatePassword()
	{
	    $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ023456789";
        srand((double)microtime()*1000000);
        $i = 0;
        $pass = '' ;
        
        while ($i <= 7) {
            $num = rand() % 57;
            $tmp = substr($chars, $num, 1);
            $pass = $pass . $tmp;
            $i++;
        }

        return $pass;
	}
	
	/**
	 * Password action
	 */
	public function passwordAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'gadmin')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            // Setup Registry
    	    $registry = Zend_Registry::getInstance();
        
            $userid = $this->view->user->user_id;
            
            if($this->_request->isPost()) :
            
                $password = $this->_request->getParam('oldpassword');
            
                // Get User data
    	        $select = $registry->db->select()
    					               ->from(array('u' => 'users'))
				   		               ->where('user_id = ?',$userid)
				   		               ->where('user_password = MD5(CONCAT(?, user_salt))',$registry->site->site->key.$password)
    					               ->limit(1,0);

    	        // Set the data array
		        $userArray = $registry->db->fetchall($select);
		    
		        if (count($userArray)) :
            
	                $options = array();

                    $filters = array();
                
                    $validators = array(
                    	'oldpassword' => array(
                        	'presence' => 'required',
                    		'NotEmpty',
                			'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Current Password - you did not enter your current password")),
                    		'breakChainOnFailure' => true
                        ),
                    	'password' => array(
                        	'presence' => 'required',
                    		'NotEmpty',
                            new Zend_Validate_StringLength(8),
                			'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing New Password - you did not enter your new password"),array( Zend_Validate_StringLength::TOO_SHORT => "Invalid Password - passwords must be at least 8 characters")),
                    		'breakChainOnFailure' => true
                        ),
                    	'password2' => array(
                        	'presence' => 'required',
                    		'NotEmpty',
                            new Zend_Validate_Identical($this->_request->getParam('password')),
                			'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Password - you did not enter re-enter your new password"),array( Zend_Validate_Identical::NOT_SAME => "Invalid Passwords - new passwords don't match")),
                    		'breakChainOnFailure' => true
                        )
                    );
                    
                    $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                    if ($input->isValid()) :
            
                         $salt = $this->generatePassword();     
                    
                         // Get user data together
                         $data = array(
                    		'user_password' => md5($registry->site->site->key.$input->password.$salt),
	                        'user_salt' => $salt
                         );

                         // Insert data into database
                         $registry->db->update('users', $data, 'user_id = '.$userid);
                         
                         echo '<p class="Spacer"></p>
						<div class="cUpd">Password Changed</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="passwordcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                
                     else :
                
                         // Set Error Messages
                        $this->messages = $input->getMessages();
                
                        if (isset($this->messages)) :
                            $this->_helper->messages->render($this->messages);
                            echo '<p class="Spacer"></p>
                        	<div class="cFormDS">
								<button dojoType="dijit.form.Button" type="button" id="passwordcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
							</div>';
                        endif;
                
                    endif;
             
                 else :
         
                     $this->messages = array(array('password' => 'Invalid Current Password - the password you entered is incorrect'));
                     
                     if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                        <div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="passwordcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                     endif;
                 
                endif;
                
            else :
            
                echo '<p class="Spacer"></p>
	            	<div class="cErr">User Not Specified!</div>
	            	<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="passwordcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';   
                
            endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
	 * Save action
	 */
	public function profileAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'gadmin')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
		    $id = $this->view->user->user_id;
        
            if (isset($id)) :
        
	            $options = array();

                $filters = array();

                $validators = array(
                	'first' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "First Name is required"))
                    ),
                    'last' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Last Name is required"))
                    ),
            		'alias' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_alias','exclude' => array('field' => 'user_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Alias is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Alias"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Alias in use"))
                    ),
                	'email' => array(
                		'presence' => 'required',
                		'NotEmpty',
                    	'EmailAddress',
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email','exclude' => array('field' => 'user_id','value' => $id))),
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "E-mail Address is required"),array(Zend_Validate_EmailAddress::INVALID => "Invalid E-mail Address"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "E-mail Address in use")),
                    	'breakChainOnFailure' => true
                    ),
                	'phone' => array('allowEmpty' => true),
                    'blurb' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                // setup database
 	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                   // Create our data array
                   $userdata = array(
                		'user_alias'	    => $input->alias,
                    	'user_email'	    => $input->email
                   );

                   // Insert data into database
                   $registry->db->update('users', $userdata, 'user_id = '.$id);
               
                   // Create our data array
                   $profiledata = array(
                    	'upro_first'	    => $input->first,
                        'upro_last'	        => $input->last,
               			'upro_phone'	    => $input->phone,
                    	'upro_blurb'	    => $input->blurb,
                    	'upro_date'			=> new Zend_Db_Expr('NOW()')
                   );

                   // Insert data into database
                   $registry->db->update('users_profiles', $profiledata, 'upro_userid = '.$id);
                   
                   echo '<p class="Spacer"></p>
					<div class="cUpd">Profile Updated</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="proupdatecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                        <div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="proupdatecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                    endif;
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
	            	<div class="cErr">User Not Specified!</div>
	            	<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="proupdatecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	
	/**
	 * Edit action
	 */
	public function indexAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'gadmin')) :
	    
		    $this->setLayout();
		
		    $userid = $this->view->user->user_id;

		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_id = ?', $userid)
				   		       ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		       ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    	                       ->limit(1,0);

		    // Set the data array
		    $userArray = $registry->db->fetchall($select);

            $this->view->userArray = $userArray['0'];
            
            // Get subscription data
    	    $select = $registry->db->select()
    					       ->from(array('g' => 'mail_groups'))
				   		       ->where('g.mgroup_open = ?','Y')
    					       ->order('g.mgroup_title ASC');

    	    // Set the data array
		    $this->view->listsArray = $registry->db->fetchall($select);
        
            if(!count($userArray)) :
                $this->_helper->redirector('manage','users','admin');
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'gadmin')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $userid = $this->view->user->user_id;
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_id = ?', $userid)
				   		       ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		       ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    	                       ->limit(1,0);

		    // Set the data array
		    $userArray = $registry->db->fetchall($select);

            $this->view->userArray = $userArray['0'];
            
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
}