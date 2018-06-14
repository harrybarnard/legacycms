<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Comments
{
    /**
     * Constructor
     * @return void
     */
    public function __construct ()
    {
        $this->registry = Zend_Registry::getInstance();
    }
    
	/**
     *  Fetches articles based on filter params and returns a paginator array 
     *  @param array $params Array of params to filter by
     *  @return array $paginator Paginated array of articles
     */
	public function fetchComments($params)
	{
	    if(isset($params['order'])) :
    	    $order = strtoupper($params['order']);
    	else :
    	    $order = 'DESC';
        endif;
        
    	$select = $this->registry->db->select();
    	$select->from(array('c' => 'comments'));
		$select->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role'));
		$select->join(array('r' => 'users_roles'),'u.user_role = r.role_id',array('r.role_colour'));
    	
		if(isset($params['type'])) :
    	    $select->where('comment_type = ?', $params['type']);
    	endif;
    	
    	if(isset($params['slave'])) :
    	    $select->where('comment_slave = ?', $params['slave']);
    	endif;
    	
    	if(isset($params['author'])) :
    	    $select->where('comment_user = ?', $params['author']);
    	endif;
    	
    	if(isset($params['status'])) :
    	    $select->where('comment_approved = ?', $params['status']);
    	endif;
    	
    	$select->order('c.comment_date '.$order);
    	
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
    
    public function deleteComment($id)
    {
        if ($id != NULL && is_numeric($id)) :
        
		    $this->registry->db->delete('comments', 'comment_id = '.$id);
		    
		else :
		    
		    throw new Exception('Invalid comment id');
		    
        endif;
    }
    
    public function deleteSlaveComment($type,$slave)
    {
        if ($type != NULL & $slave != NULL && is_numeric($slave)) :
        
		    $this->registry->db->delete('comments', array('comment_type = "'.$type.'"','comment_slave = '.$slave));
		    
		else :
		    
		    throw new Exception('Invalid parameters');
		    
        endif;
    }
}
?>