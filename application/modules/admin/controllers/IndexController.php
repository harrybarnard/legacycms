<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_IndexController extends CMS_Controller_Action_Admin
{
	public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}
	
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$this->setLayout();
	}
	
	/**
	 * The default action - show the home page
	 */
	public function createindexesAction() {
		$this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender();
        
        $this->_helper->searchIndex->create();
	}
	
	/**
     * dbsync action - sync existing db to app db
     */
    public function dbsyncAction ()
    {    
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        // Setup Registry
    	$registry = Zend_Registry::getInstance();
    	
    	// Get Article data
    	$select = $registry->db->select()
    					   ->from(array('u' => 'usersbk'))
    					   ->order('u.user_id ASC');

    	// Set the data array
		$userArray = $registry->db->fetchall($select);
        
        foreach ($userArray as $user) :
        
            // Create our data array
            $userdata = array(
            	'user_alias'        => $user['user_username'],
                'user_role'			=> '1',
    			'user_email'        => $user['user_email'],
    			'user_password'     => md5($user['user_password']),
                'user_key'	        => md5($user['user_email']),
                'user_status'	    => 'active',
                'user_date'		    => new Zend_Db_Expr('NOW()')
            );
            
            // Insert data into database
            $registry->db->insert('users', $userdata);
            
            if ($user['user_address2'] == NULL & $user['user_address3'] == NULL) :
                $useraddress = ucwords($user['user_address1']);
            elseif ($user['user_address3'] == NULL) :
                $useraddress = ucwords($user['user_address1']).', '.ucwords($user['user_address2']);
            else :
                $useraddress = ucwords($user['user_address1']).', '.ucwords($user['user_address2']).', '.ucwords($user['user_address3']);
            endif;
            
            $userid = $registry->db->lastInsertId();

            // Create our data array
            $profiledata = array(
            	'upro_userid'       => $userid,
    			'upro_name'         => ucwords($user['user_firstname']).' '.ucwords($user['user_lastname']),
    			'upro_organisation' => ucwords($user['user_organisation']),
            	'upro_position'     => ucwords($user['user_orgposition']),
            	'upro_address'      => $useraddress,
            	'upro_city'         => ucwords($user['user_city']),
                'upro_gender'		=> 'N',
            	'upro_postcode'     => strtoupper($user['user_postcode']),
            	'upro_country'      => $user['user_country'],
            	'upro_phone'        => $user['user_phone'],
                'upro_date'		    => new Zend_Db_Expr('NOW()')
            );

            // Insert data into database
            $registry->db->insert('users_profiles', $profiledata);
            
            // Get user data together
            $data = array(
            	'msub_group' => '1',
                'msub_user'	 => $userid
            );
                        
            // Insert data into database
            $registry->db->insert('mail_subscriptions', $data);
            
        endforeach;
		
    }
}