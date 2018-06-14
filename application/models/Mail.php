<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Mail
{
	/**
     * Creates a custom formatted date
     * @param string $format Format to apply
     * @param string $date Date to be formatted
     */
    private function makeDate($format,$date = NULL)
    {
        if($date != NULL) :
            $formatted = strtotime($date);
            return date($format,$formatted);
        else :
            return date($format);
        endif;
    }
	
	/**
     * Constructor
     * @return void
     */
    public function __construct()
    {
        $this->registry = Zend_Registry::getInstance();
    }
	
	/**
     *  Creates an array of valid filter params
     *  @param array $params Array of params to add to filter
     *  @return array $filter Array of valid filter params
     */
    public function getFilter($params)
	{
	    $filter = array();
	    
	    if (isset($params['group'])) :
            $select = $this->registry->db->select()
    					       ->from(array('g' => 'mail_groups'),array('g.mgroup_title'))
    					       ->where('g.mgroup_id = ?', $params['group']);

		    $groupArray = $this->registry->db->fetchAll($select);
		
		    if (count($groupArray) > 0) :
                $groupArray = $groupArray[0];
                $groupname = $groupArray['mgroup_title'];
                $filter['group'] = $groupname;
            endif;
        endif;
        
        if (isset($params['role'])) :
            $select = $this->registry->db->select()
    					       ->from(array('r' => 'users_role'),array('r.role_title'))
    					       ->where('r.role_id = ?', $params['role']);

		    $roleArray = $this->registry->db->fetchAll($select);
		
		    if (count($roleArray) > 0) :
                $roleArray = $roleArray[0];
                $rolename = $roleArray['role_title'];
                $filter['role'] = $rolename;
            endif;
        endif;
        
        if (isset($params['user'])) :
            $select = $this->registry->db->select()
    					       ->from(array('u' => 'users'),array('u.user_alias'))
    					       ->where('u.user_id = ?', $params['user']);

		    $userArray = $this->registry->db->fetchAll($select);
		
		    if (count($userArray) > 0) :
                $userArray = $userArray[0];
                $username = $userArray['user_alias'];
                $filter['user'] = $username;
            endif;
        endif;
        
        if (isset($params['author'])) :
            $select = $this->registry->db->select()
    					       ->from(array('u' => 'users'),array('u.user_alias'))
    					       ->where('u.user_id = ?', $params['author']);

		    $authorArray = $this->registry->db->fetchAll($select);
		
		    if (count($authorArray) > 0) :
                $authorArray = $authorArray[0];
                $authorname = $authorArray['user_alias'];
                $filter['author'] = $authorname;
            endif;
        endif;
        
        if (isset($params['status'])) :
            if($params['status'] == 'sent') :
                $filter['status'] = 'sent';
            elseif ($params['status'] == 'draft') :
                $filter['status'] = 'draft';
            endif;
        endif;
        
        if (isset($params['sort'])) :
            $filter['sort'] = $params['sort'];
        else :
            $filter['sort'] = 'date';
        endif;
        
        if (isset($params['order'])) :
            if ($params['order'] == 'desc') :
                $filter['order'] = 'desc';
                $filter['orderopt'] = 'asc';
            else :
                $filter['order'] = 'asc';
                $filter['orderopt'] = 'desc';
            endif;
        else :
            $filter['order'] = 'desc';
            $filter['orderopt'] = 'asc';
        endif;
	    
	    return $filter;
	}
	
	/**
     *  Fetches mail based on filter params and returns a paginator array 
     *  @param array $params Array of params to filter by
     *  @return array $paginator Paginated array of mail
     */
	public function fetchMails($params)
	{
        if(isset($params['order'])) :
    	    $order = strtoupper($params['order']);
    	else :
    	    $order = 'DESC';
        endif;
        
    	$select = $this->registry->db->select();
    	$select->from(array('m' => 'mail'));
		$select->join(array('u' => 'users'),'m.mail_user = u.user_id',array('u.user_id','u.user_alias'));
    	
		if(isset($params['group'])) :   
		    $select->where('m.mail_type = ?', 'G');
    	    $select->where('m.mail_slave = ?', $params['group']);
    	endif;
    	
    	if(isset($params['role'])) :   
		    $select->where('m.mail_type = ?', 'R');
    	    $select->where('m.mail_slave = ?', $params['role']);
    	endif;
    	
    	if(isset($params['user'])) :   
		    $select->where('m.mail_type = ?', 'U');
    	    $select->where('m.mail_slave = ?', $params['user']);
    	endif;
    	
    	if(isset($params['author'])) :
    	    $select->where('m.mail_user = ?', $params['author']);
    	endif;
    	
    	if(isset($params['status'])) :
            if($params['status'] == 'sent') :
                $select->where('m.mail_status = ?', 'sent');
            elseif($params['status'] == 'draft') :
                $select->where('m.mail_status = ?', 'draft');
            endif;
    	endif;
    	
    	if (isset($params['sort'])) :
    	
    	    if ($params['sort'] == 'date') :
    	        $select->order('m.mail_date '.$order);
    	    elseif ($params['sort'] == 'subject') :
    	        $select->order('m.mail_subject '.$order);
    	    elseif ($params['sort'] == 'author') :
    	        $select->order('u.user_alias '.$order);
    	    endif;
    	    
    	else :
    	    $select->order('m.mail_date '.$order);
    	endif;
    	
    	if(isset($params['page']) && is_numeric($params['page'])) :
    		$pagenum = $params['page'];
    	else :
    		$pagenum = 1;
    	endif;
    	
    	if(isset($params['items']) && is_numeric($params['items'])) :
    		$items = $params['items'];
    	else :
    		$items = 15;
    	endif;
    	
    	if(isset($params['range']) && is_numeric($params['range'])) :
    		$range = $params['range'];
    	else :
    		$range = 5;
    	endif;
    	
    	$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($pagenum);
		$paginator->setItemCountPerPage($items);
		$paginator->setPageRange($range);
	    
    	return $paginator;
	}
	
	/**
     *  Fetch groups and return as array
     *  @return array $groups Array of fetched groups
     */
	public function fetchGroups()
	{
        $select = $this->registry->db->select();
    	$select->from(array('g' => 'mail_groups'));
    	$select->order('g.mgroup_title ASC');
		
    	return $groups = $this->registry->db->fetchall($select);
	}
	
	/**
     *  Fetch suscriptions and return as array
     *  @return array $subscriptions Array of fetched subscriptions
     */
	public function fetchSubscriptions($params = NULL)
	{
        $select = $this->registry->db->select();
    	$select->from(array('s' => 'mail_subscriptions'));
    	$select->join(array('u' => 'users'),'u.user_id = s.msub_user',array('u.*'));
    	if(isset($params['group'])) :
    	    $select->where('s.msub_group = ?', $params['group']);
    	endif;
    	if(isset($params['format'])) :
    	    $select->where('u.user_mailformat = ?', $params['format']);
    	endif;
		
    	return $subscriptions = $this->registry->db->fetchall($select);
	}
	
	/**
     *  Fetch users with role and return as array
     *  @return array $users Array of fetched users
     */
	public function fetchUsers($params = NULL)
	{
        $select = $this->registry->db->select();
    	$select->from(array('u' => 'users'));
    	if(isset($params['user'])) :
    	    $select->where('u.user_id = ?', $params['user']);
    	endif;
    	if(isset($params['role'])) :
    	    $select->where('u.user_role = ?', $params['role']);
    	endif;
    	if(isset($params['format'])) :
    	    $select->where('u.user_mailformat = ?', $params['format']);
    	endif;
		
    	return $users = $this->registry->db->fetchall($select);
	}
	
	/**
     *  Fetch mail and return as array
     *  @return array $mail Mail
     */
	public function fetchMail($id)
	{
    	if ($id != NULL && is_numeric($id)) :
	        $select = $this->registry->db->select()
    	                                 ->from(array('m' => 'mail'))
    	                                 ->where('m.mail_id = ?', $id)
    	                                 ->join(array('u' => 'users'),'m.mail_user = u.user_id',array('u.user_alias'))
    	                                 ->limit(1,0);

		    $mail = $this->registry->db->fetchall($select);
            return $mail['0'];
        else :
            throw new Exception('Invalid mail id');
        endif;
	}
	
	/**
     *  Fetch group and return as array
     *  @return array $group Group
     */
	public function fetchGroup($id)
	{
    	if ($id != NULL && is_numeric($id)) :
    	    $select = $this->registry->db->select()
    	                           ->from(array('g' => 'mail_groups'))
    	                           ->where('g.mgroup_id = ?', $id)
    	                           ->limit(1,0);

		    $group = $this->registry->db->fetchall($select);

            return $group['0'];
        else :
            throw new Exception('Invalid group id');
        endif;
	}
	
	/**
     *  Create new mail
     *  @param array $params Mail params
     */
	public function newMail($params)
	{
        if ($params['subject'] != NULL & $params['slave'] != NULL && is_numeric($params['slave'])) :
	        $data = array(
        		'mail_type'	        => $params['type'],
                'mail_slave'	    => $params['slave'],
                'mail_subject'	    => $params['subject'],
                'mail_status'		=> 'draft',
                'mail_text'			=> $params['text'],
                'mail_html'			=> $params['html'],
                'mail_user'	        => $params['author'],
                'mail_date'		    => new Zend_Db_Expr('NOW()')
            );

            $this->registry->db->insert('mail', $data);
        else: 
            throw new Exception('Invalid parameters');
        endif;
	}
	
	/**
     *  Create new group
     *  @param array $params Category params
     */
	public function newGroup($params)
	{
        if ($params['title'] != NULL) :
	        $data = array(
           		'mgroup_title'   => $params['title'],
                'mgroup_open'    => $params['open']
                );

            $this->registry->db->insert('mail_groups', $data);
        else :
            throw new Exception('Invalid title');
        endif;
	}
	
	/**
     *  Delete mail
     *  @param integer $id Mail id
     */
	public function deleteMail($id)
	{
	    if (isset($id) && is_numeric($id)) :
	        
		    $attachments = new Attachments();
		    $mail = new Mail();
		    
		    $attachments->deleteSlaveAttachment('M',$id);
	    
		    $this->registry->db->delete('mail', 'mail_id = '.$id);
		    
        else :
            throw new Exception('Invalid mail id');
        endif;
	}
	
	/**
     *  Delete group and move associated mail to default
     *  @param integer $id Category id
     */
	public function deleteGroup($id)
	{
        if(isset($id) & $id != 1 && is_numeric($id)) :
        
            $select = $this->registry->db->select()
    	                                 ->from(array('m' => 'mail'))
    	                                 ->where('m.mail_type = ?', 'G')
    	                                 ->where('m.mail_slave = ?', $id);

		    $mails = $this->registry->db->fetchall($select);
		    
		    foreach($mails as $mitem) :
    	    
                $data = array(
                	'mail_slave' => '1'
                );

                $this->registry->db->update('mail', $data, 'mail_id = '.$mitem['mail_id']);
                
            endforeach;
            
            $this->registry->db->delete('mail_subscriptions', 'msub_group = '.$id);
    	
		    $this->registry->db->delete('mail_groups', 'mgroup_id = '.$id);
		    
		else :
		    throw new Exception('Invalid group id');
		endif;
	}
	
	/**
     *  Updates mail 
     *  @param array $params Mail params
     */
	public function updateMail($params)
	{
         $data = array(
            'mail_slave' => $params['slave'],
            'mail_subject' => $params['subject'],
            'mail_status' => 'draft',
            'mail_text'	=> $params['text'],
            'mail_html'	=> $params['html'],
            'mail_date' => new Zend_Db_Expr('NOW()')
         );

         $this->registry->db->update('mail', $data, 'mail_id = '.$params['id']);
	}
	
	/**
     *  Update group
     *  @param array $params Group params
     */
	public function updateGroup($params)
	{
        if ($params['default'] != 'Y') :
            $default = 'N';
        else :
            $default = 'Y';
        endif;
	    
	    $data = array(
        	'mgroup_title'	        => $params['title'],
            'mgroup_description'	=> $params['description'],
            'mgroup_text'	        => $params['text'],
            'mgroup_html'	        => html_entity_decode($params['html']),
            'mgroup_open'	        => $params['open'],
            'mgroup_default'	    => $default
        );

        $this->registry->db->update('mail_groups', $data, 'mgroup_id = '.$params['id']);
	}
	
	/**
     *  Change group status
     *  @param array $params Group params
     */
	public function updateGroupStatus($params)
	{
         $data = array(
         	'mgroup_status'	=> $params['status']
         );

         $this->registry->db->update('mail_groups', $data, 'mgroup_id = '.$params['id']);
	}
}
?>