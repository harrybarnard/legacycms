<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class ArticlesController extends CMS_Controller_Action_Default
{
    public function init()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load assets configuration
        $articles = new Zend_Config_Ini('../application/configs/articles.ini', array('config'));
        $registry->set('articles', $articles);
        
        // Change to articles layout
    	$this->_helper->layout->setLayout('articles');
    }
    
	/**
     * The default action - list recent articles
     */
    public function indexAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        // Get input variables
        $this->view->params = $this->_request->getParams();
        
        // Get & Set Page Number
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
        
		// Get registry
    	$registry = Zend_Registry::getInstance();
    	
    	// Get articles from db
    	$select = $registry->db->select()
    					   ->from(array('a' => 'articles'))
				   		   ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
				   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
				   		   ->where('article_published <= NOW()')
				   		   ->where('article_status = ?','published')
                           ->order(array('a.article_sticky DESC','a.article_published DESC'));

    	// Create Paginator Object
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->view->page);
		$paginator->setItemCountPerPage($registry->articles->rows);
		$paginator->setPageRange(5);
		
		// Save Paginator as View var.
		$this->view->articlesArray = $paginator;
    }
    
	/**
     * Category action - list articles in category
     */
    public function categoryAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        // Set input variables
        $this->view->params = $this->_request->getParams();
        $category = urldecode($this->_request->getParam('variable'));
        
        // Get & Set Page Number
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if ($category != NULL) : // If Category set continue
    	
    	    $this->view->category = $category; // Pass category title to view
        
		    // Get registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get articles from db
    	    $select = $registry->db->select()
    					       ->from(array('a' => 'articles'))
				   		       ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
			    	   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
			    	   		   ->where('article_published <= NOW()')
				       		   ->where('article_status = ?','published')
				   	    	   ->where('c.acat_title = ?',$category)
    					       ->order(array('a.article_sticky DESC','a.article_published DESC'));

    	    // Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->articles->rows);
		    $paginator->setPageRange(5);
		
		    // Save Paginator as View var.
		    $this->view->articlesArray = $paginator;
		    
		else : // Else if Category not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Author action - list articles by author
     */
    public function authorAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        // Set input variables
        $this->view->params = $this->_request->getParams();
        $author = urldecode($this->_request->getParam('variable'));
        
        // Get & Set Page Number 
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if ($author != NULL) : // If Author set continue
    	
    	    $this->view->author = $author; // Pass author title to view
        
		    // Get registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get articles from db
    	    $select = $registry->db->select()
    					       ->from(array('a' => 'articles'))
				   		       ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
				   		       ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
				   		       ->where('article_published <= NOW()')
				   		       ->where('article_status = ?','published')
				   		       ->where('u.user_alias = ?',$author)
    					       ->order(array('a.article_sticky DESC','a.article_published DESC'));

    	    // Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->articles->rows);
		    $paginator->setPageRange(5);
		
		    // Save Paginator as View var.
		    $this->view->articlesArray = $paginator;
		    
		else : // Else if Author not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Archive action - list articles in month and/or year
     */
    public function archiveAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        // Set input variables
        $this->view->params = $this->_request->getParams();
        $year = $this->_request->getParam('year');
		$month = $this->_request->getParam('month');
        
        // Get & Set Page Number
		$page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if ($year != NULL & $month != NULL) : // If year and month set
    	
    	    // Build archive date    
    	    $date = mktime (12, 0, 0, $month, 1, $year);
    	    $dateArray = getdate($date);
    	    
    	    // Pass date vars to view
            $this->view->year = $dateArray['year'];
            $this->view->month = $dateArray['month'];
        
		    // Get registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get articles from db
    	    $select = $registry->db->select()
    					       ->from(array('a' => 'articles'))
				   		       ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
			    	   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
			    	   		   ->where('article_published <= NOW()')
				       		   ->where('article_status = ?','published')
				       		   ->where('month(article_published) = ?',$month)
				   		       ->where('year(article_published) = ?',$year)
    					       ->order(array('a.article_sticky DESC','a.article_published DESC'));

    	    // Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->articles->rows);
		    $paginator->setPageRange(5);
		
		    // Save Paginator as View var.
		    $this->view->articlesArray = $paginator;
		    
		elseif ($year != NULL) : // If year is set
		
		    // Build archive date    
		    $date = mktime (12, 0, 0, 1, 1, $year);
    	    $dateArray = getdate($date);
    	    
    	    // Pass year var to view
            $this->view->year = $dateArray['year'];
        
		    // Get registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get articles from db
    	    $select = $registry->db->select()
    					       ->from(array('a' => 'articles'))
				   		       ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
			    	   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
			    	   		   ->where('article_published <= NOW()')
				       		   ->where('article_status = ?','published')
				   		       ->where('year(article_published) = ?',$year)
    					       ->order(array('a.article_sticky DESC','a.article_published DESC'));

    	    // Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage($registry->articles->rows);
		    $paginator->setPageRange(5);
		
		    // Save Paginator as View var.
		    $this->view->articlesArray = $paginator;
		    
		else : // Else if Year not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Article action - display article
     */
    public function articleAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        // Set input vars
        $this->view->params = $this->_request->getParams();
        $articleid = $this->_request->getParam('id');
        
        if (isset($articleid)) : // If Article ID set continue
        
		    // Get Registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get Article from db
    	    $select = $registry->db->select()
    					       ->from(array('a' => 'articles'),array('a.*','article_day' => 'DAY(a.article_published)','article_month' => 'MONTH(a.article_published)','article_year' => 'YEAR(a.article_published)'))
				   		       ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
				   		       ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
				   		       ->join(array('p' => 'users_profiles'),'a.article_user = p.upro_userid',array('p.upro_blurb'))
				   		       ->where('article_published <= NOW()')
				   		       ->where('article_status = ?','published')
				   		       ->where('article_id = ?',$articleid)
    					       ->limit(1,0);

		    $articleArray = $registry->db->fetchall($select);
		
		    if (count($articleArray)) : // If Article exists

                // Pass Article to View    
		        $this->view->articleArray = $articleArray[0];
            
            else : // Else redirect to 404 Error
        
    	        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
            
            endif;
        
            $this->view->comment = NULL; // Reset the comment value
        
            if($this->_request->isPost()) :
                if ($this->view->authenticated) :
                    $options = array();

                    $filters = array(
            			'content' => array('StringTrim', 'StripTags')
                    );

                    $validators = array(
                        'content' => array(
                    		'presence' => 'required',
                    		'NotEmpty',
                 			'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "You did not enter a comment"))
                        ),
                    );

                    $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                    // setup database
    	            $registry = Zend_Registry::getInstance();

                    if ($input->isValid()) // If the form input validates
                    {
                        // Set moderation variable
                        if($this->view->articleArray['article_moderate'] == 'Y') :
                            $approved = 'N';
                        else :
                            $approved = 'Y';
                        endif;
                    
                        // Create our data array
                        $data = array(
                        	'comment_type'      => 'A',
    						'comment_slave'     => $this->view->articleArray['article_id'],
    						'comment_approved'  => $approved,
                			'comment_content'	=> $input->content,
                			'comment_user'	    => $this->view->user->user_id,
                			'comment_date'		=> new Zend_Db_Expr('NOW()')
                        );

                        // Insert data into database
                        $registry->db->insert('comments', $data);
                
                    } else { // If input is invalid
                        $this->view->messages = $input->getMessages(); // Pass Messages to view
                        $this->view->comment = $_POST['content']; // Set value of message to match input
                    }
                endif;
            endif;
        
		else : // Else if Article ID not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
    }
    
	/**
     * Tags action - show article tag cloud
     */
    public function tagsAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
    }
    
	/**
     * Tag action - list articles with tag
     */
    public function tagAction ()
    {    
    	$this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	$tag = urldecode($this->_request->getParam('variable'));
    	$this->view->tag = $tag;
        
		// setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('t' => 'tags'),array('t.*'))
				   		   ->join(array('a' => 'articles'),'t.tag_slave = a.article_id',array('a.*','article_day' => 'DAY(a.article_published)','article_month' => 'MONTH(a.article_published)','article_year' => 'YEAR(a.article_published)'))
    					   ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
				   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
				   		   ->where('article_status = ?','published')
				   		   ->where('t.tag_tag = ?',$tag)
				   		   ->where('t.tag_type = ?','A')
    					   ->order(array('a.article_sticky DESC','a.article_published DESC'));

    	// Create Paginator Object
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->view->page);
		$paginator->setItemCountPerPage($registry->articles->rows);
		$paginator->setPageRange(5);
		
		// Save Paginator as View var.
		$this->view->articlesArray = $paginator;
    }
    
	/**
     * Atom action - create atom feed of articles
     */
    public function atomAction ()
    {    
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        $registry = Zend_Registry::getInstance();
            
        $this->view->params = $this->_request->getParams();
        
      	/**
      	* Create the parent feed
      	*/
          $feed = new Zend_Feed_Writer_Feed;
          $feed->setTitle($registry->articles->feedtitle);
          $feed->setDescription($registry->articles->feeddescription);
          $feed->setLink($registry->articles->feedlink);
          $feed->setFeedLink($registry->site->site->url.'/articles/atom', 'atom');
          $feed->addAuthor(array(
          	'name'  => $registry->articles->defaultauthor,
          	'email' => $registry->articles->defaultemail,
          	'uri'   => $registry->site->site->url,
          ));
          $feed->setDateModified(time());
          
    	  // Get articles from db
    	  $select = $registry->db->select()
    					     ->from(array('a' => 'articles'))
				   		     ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
			    	   		 ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias','u.user_email'))
			    	   		 ->where('article_published <= NOW()')
				       		 ->where('article_status = ?','published')
    					     ->order('a.article_published DESC')
    					     ->limit($registry->articles->feedcount,0);;
       
      	  $articlesArray = $registry->db->fetchall($select);
      	  
          foreach($articlesArray as $article) :
      	      $entry = $feed->createEntry();
              $entry->setTitle($article['article_title']);
              $entry->setLink($registry->site->site->url.'/articles/article/'.$article['article_id'].'/'.urlencode($article['article_title']).'/');
              if($registry->articles->showauthor == true) :
                  $entry->addAuthor(array(
          	      	'name'  => $article['user_alias'],
                  ));
              else :
                  $entry->addAuthor(array(
          	      	'name'  => $registry->articles->defaultauthor,
                  ));
              endif;
              $entry->setDateModified(strtotime($article['article_published']));
              $entry->setDateCreated(strtotime($article['article_published']));
              $entry->setDescription($this->_helper->strings->makeStub($article['article_intro'],350));
              $entry->setContent($article['article_intro'].$article['article_content']);
              $feed->addEntry($entry);
          endforeach;
       
          $out = $feed->export('atom');
          
          echo $out;
        
    }
    
	/**
     * RSS action - create rss feed of articles
     */
    public function rssAction ()
    {    
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        $registry = Zend_Registry::getInstance();
            
        $this->view->params = $this->_request->getParams();
        
      	/**
      	* Create the parent feed
      	*/
          $feed = new Zend_Feed_Writer_Feed;
          $feed->setTitle($registry->articles->feedtitle);
          $feed->setDescription($registry->articles->feeddescription);
          $feed->setLink($registry->articles->feedlink);
          $feed->setFeedLink($registry->site->site->url.'/articles/rss', 'rss');
          $feed->addAuthor(array(
          	'name'  => $registry->articles->defaultauthor,
          	'email' => $registry->articles->defaultemail,
          	'uri'   => $registry->site->site->url,
          ));
          $feed->setDateModified(time());
          
    	  // Get articles from db
    	  $select = $registry->db->select()
    					     ->from(array('a' => 'articles'))
				   		     ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
			    	   		 ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias','u.user_email'))
			    	   		 ->where('article_published <= NOW()')
				       		 ->where('article_status = ?','published')
    					     ->order('a.article_published DESC')
    					     ->limit($registry->articles->feedcount,0);;
       
      	  $articlesArray = $registry->db->fetchall($select);
      	  
          foreach($articlesArray as $article) :
      	      $entry = $feed->createEntry();
              $entry->setTitle($article['article_title']);
              $entry->setLink($registry->site->site->url.'/articles/article/'.$article['article_id'].'/'.urlencode($article['article_title']).'/');
              if($registry->articles->showauthor == true) :
                  $entry->addAuthor(array(
          	      	'name'  => $article['user_alias'],
                  ));
              else :
                  $entry->addAuthor(array(
          	      	'name'  => $registry->articles->defaultauthor,
                  ));
              endif;
              $entry->setDateModified(strtotime($article['article_published']));
              $entry->setDateCreated(strtotime($article['article_published']));
              $entry->setDescription($this->_helper->strings->makeStub($article['article_intro'],350));
              $entry->setContent($article['article_intro'].$article['article_content']);
              $feed->addEntry($entry);
          endforeach;
       
          $out = $feed->export('rss');
          
          echo $out;
        
    }
    
}