<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_UsersController extends CMS_Controller_Action_Admin
{
    public function init()
    {
    }
    
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}
	
    public function getRole($roleid)
	{
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
	    $select = $registry->db->select()
    					       ->from(array('r' => 'users_roles'))
    					       ->where('r.role_id = ?', $roleid);

		$roleArray = $registry->db->fetchall($select);
		
		if (count($roleArray) > 0) :

            $roleArray = $roleArray[0];
            
            return $this->view->role = $roleArray['role_title'];
                
        else :
        
            return false;
            
        endif;
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
	 * The default action
	 */
	public function indexAction() {
		$this->_helper->redirector('manage','users','admin');
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers')) :
		
	        // setup database
    	    $registry = Zend_Registry::getInstance();
		
		    $this->setLayout();
		
		    $role = $this->_request->getParam('role');
		
		    $status = $this->_request->getParam('status');
		
		    $query = trim($this->view->escape($this->_request->getParam('query')));
		
	        $page = $this->_getParam('page');
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
    	
    	    if ($role != NULL & $query != NULL & $status != NULL) :
    	        $this->view->query = $query;
    	        $this->getRole($role);
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_role = ?', $role)
    					           ->where('u.user_status = ?', $status)
    					           ->where('u.user_alias LIKE ?', '%'.$query.'%')
    					           ->orwhere('u.user_email LIKE ?', $query)
    					           ->orwhere('p.upro_first LIKE ?', '%'.$query.'%')
    					           ->orwhere('p.upro_last LIKE ?', '%'.$query.'%')
    					           ->order('p.upro_last ASC');
    	    elseif ($role != NULL & $query != NULL) :
    	        $this->view->query = $query;
    	        $this->getRole($role);
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_role = ?', $role)
    					           ->where('u.user_alias LIKE ?', '%'.$query.'%')
    					           ->orwhere('u.user_email LIKE ?', $query)
    					           ->orwhere('p.upro_first LIKE ?', '%'.$query.'%')
    					           ->orwhere('p.upro_last LIKE ?', '%'.$query.'%')
    					           ->order('p.upro_last ASC');
            elseif ($role != NULL & $status != NULL) :
    	        $this->view->status = $status;
    	        $this->getRole($role);
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_role = ?', $role)
    					           ->where('u.user_status = ?', $status)
    					           ->order('p.upro_last ASC');
    	    elseif ($status != NULL & $query != NULL) :
    	        $this->view->status = $status;
    	        $this->view->query = $query;
    	        $this->getRole($role);
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_status = ?', $status)
    					           ->where('u.user_alias LIKE ?', '%'.$query.'%')
    					           ->orwhere('u.user_email LIKE ?', $query)
    					           ->orwhere('p.upro_first LIKE ?', '%'.$query.'%')
    					           ->orwhere('p.upro_last LIKE ?', '%'.$query.'%')
    					           ->order('p.upro_last ASC');
            elseif ($query != NULL) :
                $this->view->query = $query;
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_alias LIKE ?', '%'.$query.'%')
    					           ->orwhere('u.user_email LIKE ?', $query)
    					           ->orwhere('p.upro_first LIKE ?', '%'.$query.'%')
    					           ->orwhere('p.upro_last LIKE ?', '%'.$query.'%')
    					           ->order('p.upro_last ASC');
    	    elseif ($role != NULL) :
    	        $this->getRole($role);
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_role = ?', $role)
    					           ->order('p.upro_last ASC');
            elseif ($status != NULL) :
    	        $this->view->status = $status;
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->where('u.user_status = ?', $status)
    					           ->order('p.upro_last ASC');
    	    else :
    	        $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
				   		           ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		           ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'))
    					           ->order('p.upro_last ASC');
    	    endif;

    	    //Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(15);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->usersArray = $paginator;
		    
		    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('r' => 'users_roles'))
    	                       ->where('r.role_id != 2')
    	                       ->order('r.role_title ASC');

		    // Set the data array
		    $this->view->roleArray = $registry->db->fetchall($select);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
		
	}
	
	/**
	 * Status action
	 */
	public function statusAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'ustatus')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->user = $this->_getParam('id');
            $this->view->status = $this->_getParam('status');
        
            if ($this->view->confirm == '1' & isset($this->view->user) & isset($this->view->status)) :
        
                // setup database
 	            $registry = Zend_Registry::getInstance();

                // Create our data array
                $userdata = array(
                	'user_status'    => $this->view->status
                );
        
		        // Insert data into database
                $registry->db->update('users', $userdata, 'user_id = '.$this->view->user);
		    
		        echo '<p class="Spacer"></p>
					<div class="cUpd">Status Changed</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="userstatuscancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to change this user\'s status?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="userstatusButton" value="Change" iconClass="userstatusIcon" onClick="getDialog(\'/admin/users/status/id/'.$this->view->user.'/status/'.$this->view->status.'/confirm/1/\',\'User Status\',\'detailsResponse\');">Change</button>
						<button dojoType="dijit.form.Button" type="button" id="userstatuscancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
	 * Password action
	 */
	public function passwordAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'upassword')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $userid = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->user)) :
                
                $registry = Zend_Registry::getInstance();
                
                // Build the query
    	        $select = $registry->db->select()
    					               ->from(array('u' => 'users'))
    					               ->where('u.user_id = ?', $userid)
    					               ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
    	                               ->limit(1,0);

		        // Set the data array
		        $userArray = $registry->db->fetchall($select);

                $this->view->userArray = $userArray['0'];
            
                $password = $this->generatePassword();
                $salt = $this->generatePassword();
	    
	            $data = array(
                    'user_password' => md5($registry->site->site->key.$password.$salt),
	                'user_salt' => $salt
                );

                $registry->db->update('users', $data, 'user_id = '.$userid); 
            
                $mail = new Zend_Mail();
                $mail->setBodyText('Your password has been reset by a Site Administrator.
            
Your new password is: '.$password.'
            
Regards,
'.$registry->site->site->name.'
'.$registry->site->site->url);
                $mail->setFrom($registry->site->site->email, $registry->site->site->name);
                $mail->addTo($this->view->userArray['user_email'], $this->view->userArray['upro_first'].' '.$this->view->userArray['upro_last']);
                $mail->setSubject('Password Reset');
                $mail->send($registry->mail);
		    
                echo '<p class="Spacer"></p>
					<div class="cUpd">
						Password Reset<br/><br/>
						The user has been sent an e-mail with the new password.
					</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="passwordresetcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to reset this user\'s password?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="passwordresetButton" value="Reset" iconClass="passwordIcon" onClick="getDialog(\'/admin/users/password/id/'.$userid.'/confirm/1/\',\'Reset Password\');">Reset</button>
						<button dojoType="dijit.form.Button" type="button" id="passwordresetcancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uedit')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
		    $id = $this->_getParam('id');
        
            if (isset($id)) :
        
	            $options = array(
	            	'notEmptyMessage' => "A non-empty value is required for field '%field%'" 
                );

                $filters = array();

                $validators = array(
                	'first' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "First name is required"))
                    ),
                    'last' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Last name is required"))
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
                	'role' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                        'Digits',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Role is required"),array( Zend_Validate_Digits::NOT_DIGITS => "Invalid Role")),
                    	'breakChainOnFailure' => true
                    ),
                	'country' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Country is required"))
                    ),
                	'gender' => array('allowEmpty' => true),
                    'dob' => array('allowEmpty' => true),
                    'organisation' => array('allowEmpty' => true),
                	'position' => array('allowEmpty' => true),
                	'address' => array('allowEmpty' => true),
                	'city' => array('allowEmpty' => true),
                	'phone' => array('allowEmpty' => true),
                	'postcode' => array('allowEmpty' => true),
                    'lists' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                // setup database
 	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                   // Create our data array
                   $userdata = array(
                		'user_alias'	    => $input->alias,
                    	'user_email'	    => $input->email,
                        'user_role'		    => $input->role
                   );

                   // Insert data into database
                   $registry->db->update('users', $userdata, 'user_id = '.$id);
               
                   // Create our data array
                   $profiledata = array(
                    	'upro_first'	    => $input->first,
                        'upro_last'	        => $input->last,
                		'upro_country'	    => $input->country,
                        'upro_gender'		=> $input->gender,
                        'upro_dob'          => $input->dob,
                		'upro_organisation'	=> $input->organisation,
                    	'upro_position'	    => $input->position,
               			'upro_address'	    => $input->address,
                    	'upro_city'	        => $input->city,
               			'upro_phone'	    => $input->phone,
                    	'upro_postcode'	    => $input->postcode,
                    	'upro_date'			=> new Zend_Db_Expr('NOW()')
                   );

                   // Insert data into database
                   $registry->db->update('users_profiles', $profiledata, 'upro_userid = '.$id);
                   
                   // Delete existing subscriptions
		           $registry->db->delete('mail_subscriptions', 'msub_user = '.$id);
            
		           // If data contains new subscriptions
		           if (count($this->_request->getParam('lists'))) :
		        
		               // Add each new subscription to db
		               foreach ($this->_request->getParam('lists') as $key => $value) :
            
                           // Get user data together
                           $data = array(
                    			'msub_group' => $value,
                        		'msub_user'	 => $id
                           );
                        
                           // Insert data into database
                           $registry->db->insert('mail_subscriptions', $data);
                        
                       endforeach;

                    endif;
                    
                   echo '<p class="Spacer"></p>
					<div class="cUpd">User Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="usersavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                        <div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="usersavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                    endif;
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
	            	<div class="cErr">User Not Specified!</div>
	            	<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="usersavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
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
	    
		    $this->setLayout();
		
		    $userid = $this->_getParam('id');
		
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
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uview')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $userid = $this->_getParam('id');
		
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
	
    public function newAction()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'unewuser')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
        
            // Setup Registry
    	    $registry = Zend_Registry::getInstance();
    	
            if($this->_request->isPost()) :
        
                $password = $this->createRandomPassword();
                
	            $options = array();

                $filters = array(
                	'email' => array('StringTrim', 'StringToLower')
                );
            
                $validators = array(
                	'email' => array(
                		'presence' => 'required',
                		'NotEmpty',
                    	'EmailAddress',
                        new Zend_Validate_Db_NoRecordExists('users','user_email'),
                		'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "E-mail Address is required"),array(Zend_Validate_EmailAddress::INVALID => "Invalid E-mail Address"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Invalid E-mail Address - e-mail address in use")),
                    	'breakChainOnFailure' => true
                    ),
            		'alias' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists('users','user_alias'),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Alias is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Alias"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Invalid Alias - alias in use")),
                    	'breakChainOnFailure' => true
                    ),
                	'first' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                        new Zend_Validate_Regex('/^[a-zA-Z-\' ]*$/'),
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "First Name is required"),array( Zend_Validate_Regex::NOT_MATCH => "Invalid First Name")),
                    	'breakChainOnFailure' => true
                    ),
                    'last' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                        new Zend_Validate_Regex('/^[a-zA-Z-\' ]*$/'),
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Last Name is required"),array( Zend_Validate_Regex::NOT_MATCH => "Invalid Last Name")),
                    	'breakChainOnFailure' => true
                    ),
                    'country' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Country is required"))
                    ),
                    'role' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                        'Digits',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Role is required"),array( Zend_Validate_Digits::NOT_DIGITS => "Invalid Role")),
                    	'breakChainOnFailure' => true
                    )
                );
                
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	        
    	            $key = md5($input->email.$password);
    	        
                    // Get user data together
                    $data = array(
                		'user_alias'	        => $input->alias,
                		'user_email'	        => $input->email,
                        'user_role'	            => $input->role,
                    	'user_password'		    => md5($password),
                    	'user_key'	            => $key,
                    	'user_date'		        => new Zend_Db_Expr('NOW()'),
                		'user_mailformat'       => 'text',
                    	'user_status'	        => 'active'
                    );

                    // Insert data into database
                    $registry->db->insert('users', $data);
                
                    $userid = $registry->db->lastInsertId();
                
                    // Get user profile data together
                    $profiledata = array(
            			'upro_userid'       => $userid,
    					'upro_first'        => $input->first,
                        'upro_last'         => $input->last,
                    	'upro_country'      => $input->country,
                		'upro_date'		    => new Zend_Db_Expr('NOW()')
                    );

                    // Insert data into database
                    $registry->db->insert('users_profiles', $profiledata);
                
                    $mail = new Zend_Mail();
                    $mail->setBodyText('An administrator has created your account.
                
You account details are:
            
E-mail Address: '.$input->email.'
Password: '.$password.'
            
Regards,
'.$registry->site->site->name.'
'.$registry->site->site->url);
                    $mail->setFrom($registry->site->site->email, $registry->site->site->name);
                    $mail->addTo($input->email, $input->first.' '.$input->last);
                    $mail->setSubject($registry->site->site->name.' Account Details');
                    $mail->send($registry->mail);
                    
                    echo '<p class="Spacer"></p>
                    <div class="cUpd">
                    	User account created<br/><br/>
						The user has been sent an e-mail with their account details.
                    </div>
                    <p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="usernewcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                 else :
                
                     $this->view->posted = 'N';
                 
                     // Pass input variables to populate form
                     $this->view->alias = $this->_request->getParam('alias');
                     $this->view->email = $this->_request->getParam('email');
                     $this->view->first = $this->_request->getParam('first');
                     $this->view->last = $this->_request->getParam('last');
                     $this->view->country = $this->_request->getParam('country');
                     $this->view->role = $this->_request->getParam('role');
                 
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
	 * Edit action
	 */
	public function roleAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uroleedit')) :
	    
		    $this->setLayout();
		
		    $roleid = $this->_getParam('id');
		
		    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    					           ->from(array('r' => 'users_roles'))
    					           ->where('r.role_id = ?', $roleid)
    	                           ->limit(1,0);

		    // Set the data array
		    $roleArray = $registry->db->fetchall($select);

            $this->view->roleArray = $roleArray['0'];
        
            if(!count($roleArray)) :
                $this->_helper->redirector('manage','users','admin');
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
     * Save Role Action - Update a role
     */
    public function rolesaveAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uroleedit')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->role = $this->_getParam('id');
        
            if (isset($this->view->role)) :
        
	            $options = array();

                $filters = array();

                $validators = array(
                	'title' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'colour' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                  		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Comments colour is required"))
                    ),
                	'resources' => array(
                		'allowEmpty' => true
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
  	            $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
            
                    // Create our data array
                    $data = array(
                		'role_title'	    => $input->title,
                    	'role_colour'	    => $input->colour
                    );

                    // Insert data into database
                    $registry->db->update('users_roles', $data, 'role_id = '.$this->view->role);
                
                    // If role isn't super administrator
                    if($this->view->role != 3) :
                    
                        // Delete existing subscriptions
		                $registry->db->delete('users_privileges', 'prv_role = '.$this->view->role);
                
                        // If data contains subscriptions
		                if (count($this->_request->getParam('resources'))) :
		        
		                    // Add each new subscription to db
		                    foreach ($this->_request->getParam('resources') as $key => $value) :
            
                                // Get user data together
                                $data = array(
                    				'prv_resource' => $value,
                        			'prv_role'	   => $this->view->role
                                );
                        
                                // Insert data into database
                                $registry->db->insert('users_privileges', $data);
                        
                            endforeach;
                            
                        endif;

                    endif;
                    
                    echo '<p class="Spacer"></p>
                    <div class="cUpd">Role Saved</div>
                    <p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="rolesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                        	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="rolesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                    endif;
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
	            	<div class="cErr">Role Not Specified!</div>
	            	<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="rolesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * New Role Action - create a new role
	 */
	public function rolenewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'urolenew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'roletitle' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
                	'rolecolour' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Colour is required"))
                    ),
                	'roleinherit' => array(
                		'allowEmpty' => true
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	
                    // Create our data array
                    $data = array(
                		'role_title'	    => $input->roletitle,
                		'role_colour'	    => $input->rolecolour
                    );

                    // Insert data into database
                    $registry->db->insert('users_roles', $data);
                    
                    $newrole = $registry->db->lastInsertId();
                    
                    if($input->roleinherit != NULL) :
                    
                        // Build the query
    	                $select = $registry->db->select()
    	                                   ->from(array('p' => 'users_privileges'))
    	                                   ->where('p.prv_role = ?',$input->roleinherit);

		                // Set the data array
		                $privileges = $registry->db->fetchall($select);
		                
		                foreach($privileges as $privilege) :
		                    // Create our data array
                            $data = array(
                				'prv_resource'	=> $privilege['prv_resource'],
                				'prv_role'	    => $newrole
                            );

                            // Insert data into database
                            $registry->db->insert('users_privileges', $data);
                        endforeach;
                        
                    endif;
                    
                    echo '<p class="Spacer"></p>
                    <div class="cUpd">Role Created</div>
                    <p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="rolenewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/users/role/id/'.$newrole.'\');">Continue</button>
					</div>';
                
                 else :
             
                    $this->view->posted = 'N';
            
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->roletitle = $input->roletitle;
                    $this->view->rolecolour = $input->rolecolour;
                    $this->view->roleinherit = $input->roleinherit;
                
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
     * Delete Role Action - Remove a role and move associated users to specified role
     */
    public function roledeleteAction () { 
          
        if($this->view->acl->isAllowed($this->view->user->user_role, 'uusers') & $this->view->acl->isAllowed($this->view->user->user_role, 'uroledelete')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	        
	        $this->view->role = $this->_getParam('id');

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'rolemove' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Move to is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	
    	            // Query for users with this role
    	            $select = $registry->db->select()
    	                                   ->from(array('u' => 'users'))
    	                                   ->where('u.user_role = ?', $this->view->role);

		            // Set the array of articles
		            $userArray = $registry->db->fetchall($select);
		    
		            foreach($userArray as $user) :
    	    
    	                // Create our data array
                        $data = array(
                			'user_role' => $input->rolemove
                        );

                        // Insert data into database
                        $registry->db->update('users', $data, 'user_id = '.$user['user_id']);
                
                    endforeach;
                    
    	            // Delete the role
		            $registry->db->delete('users_roles', 'role_id = '.$this->view->role);
		            
		            // Delete associated privileges
		            $registry->db->delete('users_privileges', 'prv_role = '.$this->view->role);
		            
                    echo '<p class="Spacer"></p>
						<div class="cUpd">Role Deleted</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="roledeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
                
                 else :
             
                    $this->view->posted = 'N';
            
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->rolemove = $input->rolemove;
                
                 endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
    public function normaliseAction ()
    {
        $this->_helper->layout->disableLayout(); // Disable Layouts
        $this->_helper->viewRenderer->setNoRender(true);
        
        $registry = Zend_Registry::getInstance();
        
        // Build the query
    	    $select = $registry->db->select()
    					       ->from(array('u' => 'users'))
				   		       ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
				   		       ->join(array('r' => 'users_roles'),'r.role_id = u.user_role',array('r.*'));

		    // Set the data array
		    $userArray = $registry->db->fetchall($select);
		    
		    foreach($userArray as $user) :
    
        $fullname = $user['upro_name'];
        list($first, $last) = split(' ', $fullname);
        $profiledata = array(
                    	'upro_first'	    => $first,
                        'upro_last'	        => $last,
                   );

                   // Insert data into database
                   $registry->db->update('users_profiles', $profiledata, 'upro_userid = '.$user['user_id']);
        
        
        endforeach;
        
    }

}