<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_AuthController extends CMS_Controller_Action_Auth
{
    protected $_session;
    
    protected $_redirector = null;
    
    public function init()
    {
        $this->_session = new Zend_Session_Namespace(__CLASS__);
    }
    
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin-auth');	
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

	public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index','index','admin');
            }
        } else {
            // If they aren't, they can't logout, so that action should
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('login','auth','admin');
            }
        }
    }
    
    public function loginAction()
    {
        $this->setLayout();
        
        if (isset($_SESSION['curPage'])) {
            $this->view->referPage = $_SESSION['curPage'];
        } else {
            $this->view->referPage = $this->view->baseUrl;
        }
        
        $this->view->requesturi = $this->getRequest()->getRequestUri();
        
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
                    $this->_helper->redirector('index','index','admin'); // redirect to dashboard
                } else {
                    switch ($result->getCode()) {
                        case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                            $messages = array('email' => array('Not Found'));
                            break;
                        case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                            $messages = array('password' => array('Invalid'));
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
    
    public function logoutAction()
    {
        $this->_helper->layout->disableLayout(); // Disable Layouts
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index','index','admin'); // redirect to home page
    }
    
    public function passwordAction()
    {
        $this->setLayout();
        
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
            
                $password = $this->generatePassword();
                $salt = $this->generatePassword();
	    
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
    
}