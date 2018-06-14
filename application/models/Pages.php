<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Pages
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
     *  Fetches articles based on filter params and returns a paginator array 
     *  @param array $params Array of params to filter by
     *  @return array $paginator Paginated array of articles
     */
	public function fetchPages($params)
	{
        if(isset($params['order'])) :
    	    $order = strtoupper($params['order']);
    	else :
    	    $order = 'DESC';
        endif;
        
    	$select = $this->registry->db->select();
    	$select->from(array('p' => 'pages'));
		$select->join(array('u' => 'users'),'p.page_user = u.user_id',array('u.user_alias'));
    	
    	if(isset($params['author'])) :
    	    $select->where('p.page_user = ?', $params['author']);
    	endif;
    	
    	if (isset($params['sort'])) :
    	
    	    if ($params['sort'] == 'date') :
    	        $select->order('p.page_date '.$order);
    	    elseif ($params['sort'] == 'page') :
    	        $select->order('p.page_title '.$order);
    	    elseif ($params['sort'] == 'author') :
    	        $select->order('u.user_alias '.$order);
    	    elseif ($params['sort'] == 'slug') :
    	        $select->order('p.page_slug '.$order);
    	    endif;
    	    
    	else :
    	    $select->order('p.page_date '.$order);
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
     *  Fetch page and return as array
     *  @return array $article Page
     */
	public function fetchPage($id)
	{
    	if ($id != NULL && is_numeric($id)) :
	        $select = $this->registry->db->select()
    	                                 ->from(array('p' => 'pages'))
    	                                 ->where('p.page_id = ?', $id)
    	                                 ->join(array('u' => 'users'),'p.page_user = u.user_id',array('u.user_alias'))
    	                                 ->limit(1,0);

		    $page = $this->registry->db->fetchall($select);
            return $page['0'];
        else :
            throw new Exception('Invalid page id');
        endif;
	}
	
	/**
     *  Create new page
     *  @param array $params Page params
     */
	public function newPage($params)
	{
        if ($params['title'] != NULL & $params['author'] != NULL && is_numeric($params['author'])) :

            $data = array(
        		'page_title' => $params['title'],
            	'page_user' => $params['author'],
            	'page_date' => new Zend_Db_Expr('NOW()'),
            	'page_edit' => new Zend_Db_Expr('NOW()')
            );

            $this->registry->db->insert('pages', $data);
        else: 
            throw new Exception('Invalid parameters');
        endif;
	}
	
	/**
     *  Delete page
     *  @param integer $id Page id
     */
	public function deletePage($id)
	{
	    if (isset($id) && is_numeric($id)) :
	    
	        $tags = new Tags();
            $tags->deleteSlaveTag('P',$id);		

            $search = new Search();
            $search->deleteEntry('p'.$id);
        
            $this->registry->db->delete('pages', 'page_id = '.$id);
        else :
            throw new Exception('Invalid page id');
        endif;
	}
	
	/**
     *  Updates article 
     *  @param array $params Article params
     */
	public function updatePage($params)
	{
         $data = array(
         	'page_title' => $params['title'],
            'page_content' => html_entity_decode($params['content']),
            'page_slug'	=> $params['slug'],
            'page_section' => $params['section'],
            'page_edit' => new Zend_Db_Expr('NOW()')
         );

         $this->registry->db->update('pages', $data, 'page_id = '.$params['id']);
         
         $page = $this->fetchPage($params['id']);
         
         if($page['page_status'] == 'published') :
             $docUrl = '/page/'.$params['slug'].'/';
             
             $details = NULL;
         
             $search = new Search();
             $search->updateEntry(array('key' => 'p'.$params['id'],
                       					'date' => $this->makeDate('Ymd',$page['page_published']),
                                        'title' => $params['title'],
                                        'url' => $docUrl,
                                        'details' => $details,
                                        'stub' => html_entity_decode($params['content']),
                                        'contents' => html_entity_decode($params['content'])
                                        ));
                                        
         endif;
	}
	
	/**
     *  Change page status
     *  @param array $params Page params
     */
	public function updatePageStatus($params)
	{
         $data = array(
         	'page_status' => $params['status'],
            'page_published' => new Zend_Db_Expr('NOW()')
         );

         $this->registry->db->update('pages', $data, 'page_id = '.$params['id']);
	}
	
}
?>