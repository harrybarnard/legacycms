<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class AuthController extends CMS_Controller_Action_Auth
{
    protected $_session;
    
    protected $_redirector = null;
    
    public function init()
    {
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }

	public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index','index','default');
            }
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('login','auth','default');
            }
        }
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
    
    public function loginAction()
    {
        
        if (isset($_SESSION['curPage'])) {
            $this->view->referPage = $_SESSION['curPage'];
        } else {
            $this->view->referPage = $this->view->baseUrl;
        }
        
        $this->view->requesturi = $this->getRequest()->getRequestUri();
        
        $this->view->method = $this->_request->getParam('method');
        
        if($this->_request->isPost()) // If form has been posted
        {
            $options = array(
            	'missingMessage' => "Field '%field%' is required"
           	    );

            $filters = array(
            	'email' => array('StringTrim', 'StringToLower')
                );

            $validators = array(
            	'email' => array(
                    'NotEmpty',
                	'EmailAddress',
                	'messages' => array(
                        'E-mail address is required',
                    	'Invalid e-mail address',
                    ),
                	'breakChainOnFailure' => true
                ),
            	'password' => array(
                    'NotEmpty',
                    array('StringLength', 4),
                	'messages' => array(
                        'Password is required',
                        'Password must be at least %min% characters long'
                    ),
                    'breakChainOnFailure' => true
                ),
                'remember' => array('allowEmpty' => true),
            );

            $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
        
            // setup database
    	    $registry = Zend_Registry::getInstance();

            if ($input->isValid()) {
                $authAdapter = new Zend_Auth_Adapter_DbTable(
                    $registry->db,
                	'users',
                	'user_email',
                	'user_password',
                	"MD5(CONCAT('". $registry->site->site->key . "', ?, user_salt))"
                );
                
                // get select object (by reference)
                $select = $authAdapter->getDbSelect();
                $select->where('user_status = "active"');
                    
                $authAdapter->setIdentity($input->email)
                            ->setCredential($input->password);

                $auth = Zend_Auth::getInstance();

                $result = $auth->authenticate($authAdapter);

                if ($result->isValid()) {
                    $auth->getStorage()->write($authAdapter->getResultRowObject(null, 'user_password','user_salt','user_key'));
                    if ($input->remember == '1') :
                        setcookie('loginEmail', $input->email, time()+7889231, '/', $registry->site->site->cookie, 0);
                        setcookie('loginPassword', $input->password, time()+7889231, '/', $registry->site->site->cookie, 0);
                    else:
                        setcookie('loginEmail', $input->email, time()-60, '/', $registry->site->site->cookie, 0);
                        setcookie('loginPassword', $input->password, time()-60, '/', $registry->site->site->cookie, 0);
                    endif;
                    $this->_redirector = $this->_helper->getHelper('Redirector');
                    $this->_redirector->gotoUrl($this->view->referPage);
                } else {
                    switch ($result->getCode()) {
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $messages = array('email' => array('Unable to authenticate identity'));
                            break;
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $messages = array('password' => array('Unable to authenticate identity'));
                            break;
                        default:
                            throw new Exception('Unsupported authentication failure code');
                            break;
                    }
                    $this->view->messages = $messages;
                    $this->view->email = $_POST['email'];
                }

            } else {
                $this->view->messages = $input->getMessages();
                if (isset($_POST['email'])) {
                    $this->view->email = $_POST['email'];
                }
            }
        }
    }
    
    public function passwordAction()
    {
        if($this->_request->isPost()) :
                
	        $options = array();

            $filters = array(
                'email'        => array('StringTrim', 'StringToLower')
            	
            );

            $validators = array(
                'email' => array(
                	'presence' => 'required',
                	'NotEmpty',
                    'EmailAddress',
                    new Zend_Validate_Db_RecordExists('users','user_email'),
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Missing E-mail Address - you did not enter an e-mail address"),array(Zend_Validate_EmailAddress::INVALID => "Invalid E-mail Address"),array(Zend_Validate_Db_RecordExists::ERROR_NO_RECORD_FOUND => "Invalid E-mail Address - entered e-mail address is not associated with an existing account")),
                    'breakChainOnFailure' => true
                )
            );

            $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
            if ($input->isValid()) :
            
    	        $registry = Zend_Registry::getInstance();
                
                // Build the query
    	        $select = $registry->db->select()
    					               ->from(array('u' => 'users'))
    					               ->where('u.user_email = ?', $input->email)
    					               ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
    	                               ->limit(1,0);

		        // Set the data array
		        $userArray = $registry->db->fetchall($select);

                $this->view->userArray = $userArray['0'];
            
                $password = $this->generatepassword();
                $salt = $this->generatepassword();
	    
                // Create our data array
                $userdata = array(
                	'user_password' => md5($registry->site->site->key.$password.$salt),
	                'user_salt' => $salt
                );
                
		        // Insert data into database
                $registry->db->update('users', $userdata, 'user_id = '.$this->view->userArray['user_id']);
            
                $mail = new Zend_Mail();
                $mail->setBodyText('You have received this e-mail because you (or someone else) have asked for a new password for '.$registry->site->site->name.'.
            
Your new password is: '.$password.'
            
Regards,
'.$registry->site->site->name.'
'.$registry->site->site->url);
                $mail->setFrom($registry->site->site->email, $registry->site->site->name);
                $mail->addTo($this->view->userArray['user_email'], $this->view->userArray['upro_first'].' '.$this->view->userArray['upro_last']);
                $mail->setSubject('New Password For '.$registry->site->site->name);
                $mail->send($registry->mail);
            
                $this->view->posted = 'Y';
                    
             else :
                
                 $this->view->posted = 'N';
                 
                 // Pass input variables to populate form
                 $this->view->email = $this->_request->getParam('email');
                 
                 // Set Error Messages
                 $this->view->messages = $input->getMessages();
                
            endif;
             
         else :
         
             $this->view->posted = 'N';
         
         endif;
    }
    
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout(); // Disable Layouts
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index','index','default'); // redirect to home page
    }
    
    public function registerAction()
    {
        $this->view->captcha = new Zend_Captcha_Image(array(  
        	'wordLen' => 7,  
			'font' => './_captcha/fonts/Envy Code R.ttf',
        	'fontSize' => 22,  
			'imgDir' => './_captcha/images/',  
			'imgUrl' => '/_captcha/images/',  
			'width' => 180,  
			'height' => 60,  
			'dotNoiseLevel' => 35,  
			'lineNoiseLevel' => 2    
        )); 
        
        // Setup Registry
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					       ->from(array('g' => 'mail_groups'))
				   		       ->where('g.mgroup_open = ?','Y')
    					       ->order('g.mgroup_title ASC');

    	// Set the data array
		$this->view->listsArray = $registry->db->fetchall($select);
    	
        if($this->_request->isPost()) :
        
            $password = $this->_request->getParam('password');
                
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
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Missing E-mail Address - you did not enter an e-mail address"),array(Zend_Validate_EmailAddress::INVALID => "Invalid E-mail Address"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Invalid E-mail Address - entered e-mail address is associated with an existing account")),
                    'breakChainOnFailure' => true
                ),
            	'alias' => array(
                	'presence' => 'required',
                 	'NotEmpty',
                    new Zend_Validate_Alnum(true),
                    new Zend_Validate_Db_NoRecordExists('users','user_alias'),
                    'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Alias - you did not enter an alias"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Alias - letters, numbers and spaces only"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Invalid Alias - entered alias is associated with an existing account")),
                    'breakChainOnFailure' => true
                ),
                'first' => array(
                    'presence' => 'required',
                    'NotEmpty',
                    new Zend_Validate_Regex('/^[a-zA-Z-\' ]*$/'),
                    'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing First Name - you did not enter your first name"),array( Zend_Validate_Regex::NOT_MATCH => "Invalid First Name - entered name contains invalid characters")),
                    'breakChainOnFailure' => true
                ),
                'last' => array(
                    'presence' => 'required',
                    'NotEmpty',
                    new Zend_Validate_Regex('/^[a-zA-Z-\' ]*$/'),
                    'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Last Name - you did not enter your last name"),array( Zend_Validate_Regex::NOT_MATCH => "Invalid Last Name - entered name contains invalid characters")),
                    'breakChainOnFailure' => true
                ),
                'password' => array(
                    'presence' => 'required',
                    'NotEmpty',
                    new Zend_Validate_StringLength(8),
                 	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Password - you did not enter a password"),array( Zend_Validate_Stringlength::TOO_SHORT => "Invalid Password - passwords must be at least 8 characters long")),
                    'breakChainOnFailure' => true
                ),
                'password2' => array(
                    'presence' => 'required',
                    'NotEmpty',
                    new Zend_Validate_Identical($password),
                 	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Missing Password - you did not re-enter your password"),array( Zend_Validate_Identical::NOT_SAME => "Invalid Password - passwords don't match")),
                    'breakChainOnFailure' => true
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
                'lists' => array(
                		'allowEmpty' => true
                ),
                'format' => array(
                	'allowEmpty' => true
                )
            );
                
            $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
            if ($input->isValid() & $this->view->captcha->isValid($_POST['captcha'])) :
            
    	        $registry = Zend_Registry::getInstance();
    	        
    	        $salt = $this->generatepassword(); 
    	        $ukey = md5($input->email.$input->password);
    	        
                // Get user data together
                $data = array(
                	'user_alias'	        => $input->alias,
                	'user_email'	        => $input->email,
                    'user_password'		    => md5($registry->site->site->key.$input->password.$salt),
                    'user_salt'             => $salt,
                    'user_key'	            => $ukey,
                    'user_date'		        => new Zend_Db_Expr('NOW()'),
                	'user_mailformat'       => $input->format,
                    'user_status'	        => 'inactive'
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
                	'upro_city'         => $input->city,
                	'upro_date'		    => new Zend_Db_Expr('NOW()')
                );

                // Insert data into database
                $registry->db->insert('users_profiles', $profiledata);
                
                // If data contains subscriptions
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
                
                try {
                    $mail = new Zend_Mail();
                $mail->setBodyText('Your account has been created. Before it can be used it must be activated.
                
To activate your account visit the link below:
            
'.$registry->site->site->url.'/auth/activate/id/'.$userid.'/key/'.$ukey.'/
            
Regards,
'.$registry->site->site->name.'
'.$registry->site->site->url);
                $mail->setFrom($registry->site->site->email, $registry->site->site->name);
                $mail->addTo($input->email, $input->first.' '.$input->last);
                $mail->setSubject($registry->site->site->name.' Account Activation');
                $mail->send($registry->mail);
                } catch (Zend_Exception $e) {
                    echo $e->message;
                }
                    
             else :
                
                 $this->view->posted = 'N';
                 
                 // Pass input variables to populate form
                 $this->view->alias = $this->_request->getParam('alias');
                 $this->view->email = $this->_request->getParam('email');
                 $this->view->first = $this->_request->getParam('first');
                 $this->view->last = $this->_request->getParam('last');
                 $this->view->country = $this->_request->getParam('country');
                 $this->view->city = $this->_request->getParam('city');
                 $this->view->notifications = $this->_request->getParam('notifications');
                 $this->view->format = $this->_request->getParam('format');
                
                 // Set Error Messages
                 $this->view->messages = $input->getMessages();
                 
                 if(!$this->view->captcha->isValid($_POST['captcha'])) :
                     $this->view->messages[] = array('captcha' => 'Invalid Verification Code - you must enter the code exactly as displayed');
                 endif;
                
            endif;
             
         else :
         
             $this->view->posted = 'N';
         
         endif;
         
         $this->view->id = $this->view->captcha->generate();
    }
    
    public function activateAction()
    {
        $id = $this->_request->getParam('id');
        $key = $this->_request->getParam('key');
        
        if(isset($id) & isset($key)) :
                
    	    $registry = Zend_Registry::getInstance();
    	        
            // Build the query
    	    $select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_id = ?', $id)
    					       ->where('u.user_key = ?', $key)
    					       ->where('u.user_status = ?', 'inactive')
    					       ->join(array('p' => 'users_profiles'),'p.upro_userid = u.user_id',array('p.*'))
    	                       ->limit(1,0);

		    // Set the data array
		    $userArray = $registry->db->fetchall($select);
		        
		    if (count($userArray)) :

                $this->view->userArray = $userArray['0'];
                
                // Create our data array
                $userdata = array(
                	'user_status'    => 'active'
                );
        
		        // Insert data into database
                $registry->db->update('users', $userdata, 'user_id = '.$this->view->userArray['user_id']);
                
                $mail = new Zend_Mail();
                $mail->setBodyText('Thank you for activating your account.
            
You can login to your account here: '.$registry->site->site->url.'/auth/login/

You can change your account settings here: '.$registry->site->site->url.'/settings/

If you have forgotten your password you can reset it here: '.$registry->site->site->url.'/auth/password/
            
Regards,
'.$registry->site->site->name.'
'.$registry->site->site->url);
                $mail->setFrom($registry->site->site->email, $registry->site->site->name);
                $mail->addTo($this->view->userArray['user_email'], $this->view->userArray['upro_first'].' '.$this->view->userArray['upro_last']);
                $mail->setSubject($registry->site->site->name.' Account Activated');
                $mail->send($registry->mail);
            
                $this->view->posted = 'Y';
                
                $this->_helper->redirector('login','auth','default',array('method' => 'activated'));
                    
             else :
                
                 $this->view->posted = 'N';
                 
                 $this->view->messages = array(array('data' => 'Invalid Credentials - the credentials you supplied cannot be matched to an account awaiting activation'));
                
            endif;
             
         else :
         
             $this->view->posted = 'N';
             
             $this->view->messages = array(array('data' => 'Invalid Credentials - the credentials you supplied cannot be matched to an account awaiting activation'));
         
         endif;
    }
    
}