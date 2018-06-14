<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Articles
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
        
        $articles = new Zend_Config_Ini('../application/configs/articles.ini', array('config'));
        $this->registry->set('articles', $articles);
    }
	
	/**
     *  Creates an array of valid filter params
     *  @param array $params Array of params to add to filter
     *  @return array $filter Array of valid filter params
     */
    public function getFilter($params)
	{
	    $filter = array();
	    
	    if (isset($params['category'])) :
            $select = $this->registry->db->select()
    					       ->from(array('c' => 'articles_categories'),array('c.acat_title'))
    					       ->where('c.acat_id = ?', $params['category']);

		    $categoryArray = $this->registry->db->fetchAll($select);
		
		    if (count($categoryArray) > 0) :
                $categoryArray = $categoryArray[0];
                $categoryname = $categoryArray['acat_title'];
                $filter['category'] = $categoryname;
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
	public function fetchArticles($params)
	{
        if(isset($params['order'])) :
    	    $order = strtoupper($params['order']);
    	else :
    	    $order = 'DESC';
        endif;
        
    	$select = $this->registry->db->select();
    	$select->from(array('a' => 'articles'));
    	$select->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'));
		$select->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'));
    	
		if(isset($params['category'])) :
    	    $select->where('a.article_category = ?', $params['category']);
    	endif;
    	
    	if(isset($params['author'])) :
    	    $select->where('a.article_user = ?', $params['author']);
    	endif;
    	
    	if (isset($params['sort'])) :
    	
    	    if ($params['sort'] == 'date') :
    	        $select->order('a.article_date '.$order);
    	    elseif ($params['sort'] == 'article') :
    	        $select->order('a.article_title '.$order);
    	    elseif ($params['sort'] == 'author') :
    	        $select->order('u.user_alias '.$order);
    	    elseif ($params['sort'] == 'category') :
    	        $select->order('c.acat_title '.$order);
    	    endif;
    	    
    	else :
    	    $select->order('a.article_date '.$order);
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
     *  Fetch categories and return as array
     *  @return array $categories Array of fetched categories
     */
	public function fetchCategories()
	{
        $select = $this->registry->db->select();
    	$select->from(array('c' => 'articles_categories'));
    	$select->order('c.acat_title ASC');
		
    	return $categories = $this->registry->db->fetchall($select);
	}
	
	/**
     *  Fetch article and return as array
     *  @return array $article Article
     */
	public function fetchArticle($id)
	{
    	if ($id != NULL && is_numeric($id)) :
	        $select = $this->registry->db->select()
    	                                 ->from(array('a' => 'articles'))
    	                                 ->where('a.article_id = ?', $id)
    	                                 ->join(array('c' => 'articles_categories'),'c.acat_id = a.article_category',array('c.acat_title'))
    	                                 ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
    	                                 ->limit(1,0);

		    $article = $this->registry->db->fetchall($select);
            return $article['0'];
        else :
            throw new Exception('Invalid article id');
        endif;
	}
	
	/**
     *  Fetch category and return as array
     *  @return array $category Category
     */
	public function fetchCategory($id)
	{
    	if ($id != NULL && is_numeric($id)) :
    	    $select = $this->registry->db->select()
    	                           ->from(array('c' => 'articles_categories'))
    	                           ->where('c.acat_id = ?', $id)
    	                           ->limit(1,0);

		    $category = $this->registry->db->fetchall($select);

            return $category['0'];
        else :
            throw new Exception('Invalid article id');
        endif;
	}
	
	/**
     *  Create new article
     *  @param array $params Article params
     */
	public function newArticle($params)
	{
        if ($params['title'] != NULL & $params['category'] != NULL && is_numeric($params['category']) & $params['author'] != NULL && is_numeric($params['author'])) :
	        $data = array(
        		'article_title'	    => $params['title'],
            	'article_category'	=> $params['category'],
            	'article_user'	    => $params['author'],
            	'article_date'		=> new Zend_Db_Expr('NOW()'),
            	'article_edit'		=> new Zend_Db_Expr('NOW()')
            );

            $this->registry->db->insert('articles', $data);
        else: 
            throw new Exception('Invalid parameters');
        endif;
	}
	
	/**
     *  Create new category
     *  @param array $params Category params
     */
	public function newCategory($params)
	{
        if ($params['title'] != NULL) :
	        $data = array(
        		'acat_title'    => $params['title']
            );

            $this->registry->db->insert('articles_categories', $data);
        else :
            throw new Exception('Invalid title');
        endif;
	}
	
	/**
     *  Delete article
     *  @param integer $id Article id
     */
	public function deleteArticle($id)
	{
	    if (isset($id) && is_numeric($id)) :
	    
	        $tags = new Tags();
            $tags->deleteSlaveTag('A',$id);		

            $comments = new Comments();
            $comments->deleteSlaveComment('A',$id);	
        
            $search = new Search();
            $search->deleteEntry('a'.$id);
        
            $this->registry->db->delete('articles', 'article_id = '.$id);
        else :
            throw new Exception('Invalid article id');
        endif;
	}
	
	/**
     *  Delete category and move associated articles to default
     *  @param integer $id Category id
     */
	public function deleteCategory($id)
	{
        if(isset($id) & $id != 1 && is_numeric($id)) :
	        
            $select = $this->registry->db->select()
    	                                 ->from(array('a' => 'articles'))
    	                                 ->where('a.article_category = ?', $id);

		    $articles = $this->registry->db->fetchall($select);
		    
		    foreach($articles as $article) :
    	    
                $data = array(
                	'article_category' => '1'
                );

                $this->registry->db->update('articles', $data, 'article_id = '.$article['article_id']);
                
            endforeach;
    	
		    $this->registry->db->delete('articles_categories', 'acat_id = '.$id);
		    
		else :
		    throw new Exception('Invalid category id');
		endif;
	}
	
	/**
     *  Updates article 
     *  @param array $params Article params
     */
	public function updateArticle($params)
	{
         if ($params['comments'] != 'Y') :
             $comments = 'N';
         else :
             $comments = 'Y';
         endif;
            
         if ($params['moderate'] != 'Y') :
             $moderate = 'N';
         else :
             $moderate = 'Y';
         endif;
            
         if ($params['sticky'] != 'Y') :
             $sticky = 'N';
         else :
             $sticky = 'Y';
         endif;
        
         $data = array(
         	'article_title'	    => $params['title'],
            'article_category'	=> $params['category'],
            'article_intro'	    => html_entity_decode($params['introduction']),
            'article_content'	=> html_entity_decode($params['content']),
            'article_comments'	=> $comments,
            'article_moderate'	=> $moderate,
            'article_sticky'	=> $sticky,
            'article_edit'		=> new Zend_Db_Expr('NOW()')
         );

         $this->registry->db->update('articles', $data, 'article_id = '.$params['id']);
         
         $article = $this->fetchArticle($params['id']);
         
         if($article['article_status'] == 'published') :
             $docUrl = '/articles/article/'.$params['id'].'/'.urlencode($params['title']).'/';
             
             if($this->registry->articles->showauthor == true) :
                 $details = 'Written by '.$article['user_alias'].' on '.$this->makeDate('l jS F Y',$article['article_published']);
             else :
                 $details = 'Written on '.$this->makeDate('l jS F Y',$article['article_published']);
             endif;
         
             $search = new Search();
             $search->updateEntry(array('key' => 'a'.$params['id'],
                       					'date' => $this->makeDate('Ymd',$article['article_published']),
                                        'title' => $params['title'],
                                        'url' => $docUrl,
                                        'details' => $details,
                                        'stub' => html_entity_decode($params['introduction']),
                                        'contents' => html_entity_decode($params['introduction']).html_entity_decode($params['content'])
                                        ));
                                        
         endif;
	}
	
	/**
     *  Change article status
     *  @param array $params Article params
     */
	public function updateArticleStatus($params)
	{
         $data = array(
         	'article_status'	=> $params['status'],
            'article_published'	=> new Zend_Db_Expr('NOW()')
         );

         $this->registry->db->update('articles', $data, 'article_id = '.$params['id']);
	}
	
	/**
     *  Update category
     *  @param array $params Category params
     */
	public function updateCategory($params)
	{
        $data = array(
        	'acat_title' => $params['title']
        );

        $this->registry->db->update('articles_categories', $data, 'acat_id = '.$params['id']);
	}
}
?>