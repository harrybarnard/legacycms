<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class SettingsController extends CMS_Controller_Action_Default
{
    
    public function init()
    {
        // Change to articles layout
    	$this->_helper->layout->setLayout('main');
    }
    
    private function generatepassword()
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
     * Index action - display article
     */
    public function indexAction ()
    {    
        $this->setRefer(); // Set auth referer
        
        if (Zend_Auth::getInstance()->hasIdentity()) :
        
            // Setup Registry
    	    $registry = Zend_Registry::getInstance();
        
            $userid = $this->view->user->user_id;
    	
    	    // Get User data
    	    $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
				   		           ->join(array('p' => 'users_profiles'),'u.user_id = p.upro_userid',array('p.*'))
				   		           ->where('user_id = ?',$userid)
    					           ->limit(1,0);

    	    // Set the data array
		    $userArray = $registry->db->fetchall($select);
		
		    // Pass Article to View    
		    $this->view->userArray = $userArray[0];
        
            if($this->_request->isPost()) :
        
                $password = $this->_request->getParam('password');
                
	            $options = array();

                $filters = array();
                
                $validators = array(
                    'email' => array(
                		'allowEmpty' => true
                    ),
            		'alias' => array(
                		'allowEmpty' => true
                    ),
                	'first' => array(
                    	'allowEmpty' => true
                    ),
                    'last' => array(
                    	'allowEmpty' => true
                    ),
                	'country' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                        new Zend_Validate_Regex('/^[a-zA-Z-\'\(\) ]*$/'),
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Country - you did not enter a country"),array( Zend_Validate_Regex::NOT_MATCH => "Invalid Country - you did not enter a valid country")),
                    	'breakChainOnFailure' => true
                    ),
                	'city' => array(
                        'allowEmpty' => true,
                        new Zend_Validate_Regex('/^[a-zA-Z-\'\(\) ]*$/'),
                 	    'messages' => array( array( Zend_Validate_Regex::NOT_MATCH => "Invalid City - you did not enter a valid city")),
                        'breakChainOnFailure' => true
                    ),
                	'format' => array(
                		'allowEmpty' => true
                    )
                );
                    
                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                     // Get user data together
                     $data = array(
                		'user_mailformat'       => $input->format
                     );

                     // Insert data into database
                     $registry->db->update('users', $data, 'user_id = '.$userid);
                
                     // Get user profile data together
                     $profiledata = array(
            			'upro_country'      => $input->country,
                		'upro_city'         => $input->city,
                        'upro_phone'        => $input->phone,
                		'upro_date'		    => new Zend_Db_Expr('NOW()')
                     );

                     // Insert data into database
                     $registry->db->update('users_profiles', $profiledata, 'upro_userid = '.$userid);
                     
                     // Pass input variables to populate form
                     $this->view->country = $input->country;
                     $this->view->city = $input->city;
                     $this->view->phone = $input->phone;
                     $this->view->format = $input->format;
                
                 else :
                
                     $this->view->posted = 'N';
                     
                     if($this->_request->getParam('notifications') == '1') :
    	                $notifications = 'Y';
    	             else :
    	                $notifications = 'N';
    	             endif;
                 
                     // Pass input variables to populate form
                     $this->view->country = $this->_request->getParam('country');
                     $this->view->city = $this->_request->getParam('city');
                     $this->view->phone = $this->_request->getParam('phone');
                     $this->view->format = $this->_request->getParam('format');
                 
                     // Set Error Messages
                     $this->view->messages = $input->getMessages();
                
                endif;
             
             else :
         
                 $this->view->posted = 'N';
                 
                 $this->view->params = $this->_request->getParams(); // Pass params to view
        
		         // Pass input variables to populate form
                 $this->view->country = $this->view->userArray['upro_country'];
                 $this->view->city = $this->view->userArray['upro_city'];
                 $this->view->phone = $this->view->userArray['upro_phone'];
                 $this->view->format = $this->view->userArray['user_mailformat'];
         
            endif;
        
		else :
		
		    $this->_helper->layout->setLayout('main');	    
            $this->_forward('login','auth','default');
		
        endif;
    }
    
	/**
     * Password action - change account password
     */
    public function passwordAction ()
    {    
        $this->setRefer(); // Set auth referer
        
        if (Zend_Auth::getInstance()->hasIdentity()) :
        
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
                    
                         $salt = $this->generatepassword();     
                    
                         // Get user data together
                         $data = array(
                    		'user_password' => md5($registry->site->site->key.$input->password.$salt),
	                        'user_salt' => $salt
                         );

                         // Insert data into database
                         $registry->db->update('users', $data, 'user_id = '.$userid);
                
                     else :
                
                         $this->view->posted = 'N';
                     
                         // Set Error Messages
                         $this->view->messages = $input->getMessages();
                
                    endif;
             
                 else :
         
                     $this->view->posted = 'N';
                     
                     $this->view->messages = array(array('password' => 'Invalid Current Password - the password you entered is incorrect'));
                 
                endif;
                
            else :
            
                $this->view->posted = 'N';    
                
            endif;
        
		else :
		
		    $this->_helper->layout->setLayout('main');	    
            $this->_forward('login','auth','default');
		
        endif;
    }
    
	/**
     * Subscriptions action - mailing list subscriptions settings
     */
    public function subscriptionsAction ()
    {    
        $this->setRefer(); // Set auth referer
        
        // If authenticated user
        if (Zend_Auth::getInstance()->hasIdentity()) :
        
            $this->view->params = $this->_request->getParams(); // Pass params to view
        
            // Setup Registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get subscription data
    	    $select = $registry->db->select()
    					       ->from(array('g' => 'mail_groups'))
				   		       ->where('g.mgroup_open = ?','Y')
    					       ->order('g.mgroup_title ASC');

    	    // Set the data array
		    $this->view->listsArray = $registry->db->fetchall($select);
		    
            $userid = $this->view->user->user_id;
            
            // If data posted
            if($this->_request->isPost()) :
            
                // Delete existing subscriptions
		        $registry->db->delete('mail_subscriptions', 'msub_user = '.$userid);
            
		        // If data contains new subscriptions
		        if (count($this->_request->getParam('lists'))) :
		        
		            // Add each new subscription to db
		            foreach ($this->_request->getParam('lists') as $key => $value) :
            
                        // Get user data together
                        $data = array(
                    		'msub_group' => $value,
                        	'msub_user'	 => $userid
                        );
                        
                        // Insert data into database
                        $registry->db->insert('mail_subscriptions', $data);
                        
                    endforeach;

                endif;
                
            // If data not posted
            else :
            
                $this->view->posted = 'N';    
                
            endif;
		    
		// If user not authenticated
        else :
		
		    $this->_helper->layout->setLayout('main');	    
            $this->_forward('login','auth','default');
		
        endif;
    }
    
	/**
     * Alias Action - check if alias is available
     */
    public function aliascheckAction()
    {
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        // Then get the query, defaulting to an empty string
        $alias = $this->_getParam('alias');
        
        // setup database
    	$registry = Zend_Registry::getInstance();
        
        // Build the query
    	$select = $registry->db->select()
    	                       ->from(array('u' => 'users'))
    	                       ->where('u.user_alias = ?', $alias);

		// Set the data array
		$aliasArray = $registry->db->fetchall($select);
    					   
    	if(strlen($alias) >= 3) :
		
		    if(count($aliasArray)) :
    	        echo '<div class="aErr">Alias is in use</div>';
    	    else :
    	        echo '<div class="aInfo">Alias available</div>';
    	    endif;
    	
    	endif;
    }
    
	/**
     * Password Action - check if alias is available
     */
    public function passwordcheckAction()
    {
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        // Then get the query, defaulting to an empty string
        $password = $this->_getParam('password');
        
        $ps = new CMS_Password_Strength();
        
        $ps->setPassword($password);
        $ps->calculateAll();
        $info = $ps->getInfo();
        
        if(strlen($password) >= 8) :
        
            if($info['averageScore'] <= 25) :
        
                echo '<div class="aErr">Very weak password</div>';
            
            elseif($info['averageScore'] <= 50) :
        
                echo '<div class="aErr">Weak password</div>';
            
            elseif($info['averageScore'] <= 60) :
        
                echo '<div class="aInfo">Quite strong password</div>';
            
            elseif($info['averageScore'] <= 80) :
        
                echo '<div class="aInfo">Strong password</div>';
            
            else :
        
                echo '<div class="aInfo">Very strong password</div>';
            
            endif;
            
        endif;
            
    }
    
}