<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class ProductsController extends CMS_Controller_Action_Default
{
    
    public function init()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load events configuration
        $products = new Zend_Config_Ini('../application/configs/products.ini', array('config'));
        $registry->set('products', $products);
        
        // Change to products layout
    	$this->_helper->layout->setLayout('products');
    }

	/**
     * The default action - list resource categories
     */
    public function indexAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('c' => 'products_categories'))
    					   ->order('c.pcat_title ASC');
    					   
    	// Set the gallery array
		$this->view->categoryArray = $registry->db->fetchall($select);
    }
    
	/**
     * Category action - list resources in category
     */
    public function categoryAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
        
        // Set input variables
        $this->view->params = $this->_request->getParams();
        $category = urldecode($this->_request->getParam('variable'));
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	if ($category != NULL) : // If Category set continue
    	
    	    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $select = $registry->db->select()
    					       ->from(array('c' => 'resources_categories'))
				   		       ->where('c.rcat_title = ?',$category)
				   		       ->limit(1,0);
				   		       
		    $categoryArray = $registry->db->fetchall($select);
		    
		    // If category exists continue
		    if (count($categoryArray)) :
		    
		        $this->view->category = $category;
		        
		        // Pass category array to View    
		        $this->view->categoryArray = $categoryArray[0];
        
                require_once "Zend/Loader.php";
		        Zend_Loader::loadClass('Zend_Paginator');
		
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		           ->join(array('t' => 'resources_types'),'t.rtype_id = r.resource_type')
				   		           ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       		       ->where('r.resource_status = ?','published')
				   	    	       ->where('c.rcat_title = ?',$category)
    					           ->order('r.resource_title ASC');

    	        //Create Paginator Object
		        $paginator = Zend_Paginator::factory($select);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->resources->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->resourcesArray = $paginator;
		        
		    else : // Else if category doesn't exists redirect to 404 error
		    
		        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
                
            endif;
		    
		else : // Else if Category not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Type action - list resources of type
     */
    public function typeAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	$type = urldecode($this->_request->getParam('variable'));
    	
    	if ($type != NULL) : // If Category set continue
    	
    	    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'resources_types'))
				   		       ->where('t.rtype_title = ?',$type)
				   		       ->limit(1,0);
				   		       
		    $typeArray = $registry->db->fetchall($select);
		    
		    // If category exists continue
		    if (count($typeArray)) :
		    
		        $this->view->type = $type;
		        
		        // Pass category array to View    
		        $this->view->typeArray = $typeArray[0];
        
                require_once "Zend/Loader.php";
		        Zend_Loader::loadClass('Zend_Paginator');
		
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		           ->join(array('t' => 'resources_types'),'t.rtype_id = r.resource_type')
				   		           ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       		       ->where('r.resource_status = ?','published')
				   	    	       ->where('t.rtype_title = ?',$type)
    					           ->order('r.resource_title ASC');

    	        //Create Paginator Object
		        $paginator = Zend_Paginator::factory($select);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->resources->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->resourcesArray = $paginator;
		        
		    else : // Else if type doesn't exists redirect to 404 error
		    
		        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
                
            endif;
		    
		else : // Else if type not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Author action - list resources by author
     */
    public function authorAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	$author = urldecode($this->_request->getParam('variable'));
    	
    	if ($author != NULL) : // If author set continue
    	
    	    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $select = $registry->db->select()
    					       ->from(array('r' => 'resources'))
    					       ->where('r.resource_type = ?','1')
				   		       ->where('r.resource_author = ?',$author)
				   		       ->limit(1,0);
				   		       
		    $authorArray = $registry->db->fetchall($select);
		    
		    // If author exists continue
		    if (count($authorArray)) :
		    
		        $this->view->author = $author;
		        
		        // Pass author array to View    
		        $this->view->authorArray = $authorArray[0];
        
                require_once "Zend/Loader.php";
		        Zend_Loader::loadClass('Zend_Paginator');
		
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		           ->join(array('t' => 'resources_types'),'t.rtype_id = r.resource_type')
				   		           ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       		       ->where('r.resource_status = ?','published')
				   	    	       ->where('r.resource_author = ?',$author)
    					           ->order('r.resource_title ASC');

    	        //Create Paginator Object
		        $paginator = Zend_Paginator::factory($select);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->resources->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->resourcesArray = $paginator;
		        
		    else : // Else if author doesn't exists redirect to 404 error
		    
		        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
                
            endif;
		    
		else : // Else if author not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Illustrator action - list resources by illustrator
     */
    public function illustratorAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	$illustrator = urldecode($this->_request->getParam('variable'));
    	
    	if ($illustrator != NULL) : // If illustrator set continue
    	
    	    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $select = $registry->db->select()
    					       ->from(array('r' => 'resources'))
    					       ->where('r.resource_type = ?','1')
				   		       ->where('r.resource_illustrator = ?',$illustrator)
				   		       ->limit(1,0);
				   		       
		    $illustratorArray = $registry->db->fetchall($select);
		    
		    // If illustrator exists continue
		    if (count($illustratorArray)) :
		    
		        $this->view->illustrator = $illustrator;
		        
		        // Pass category array to View    
		        $this->view->illustratorArray = $illustratorArray[0];
        
                require_once "Zend/Loader.php";
		        Zend_Loader::loadClass('Zend_Paginator');
		
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		           ->join(array('t' => 'resources_types'),'t.rtype_id = r.resource_type')
				   		           ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       		       ->where('r.resource_status = ?','published')
				   	    	       ->where('r.resource_illustrator = ?',$illustrator)
    					           ->order('r.resource_title ASC');

    	        //Create Paginator Object
		        $paginator = Zend_Paginator::factory($select);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->resources->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->resourcesArray = $paginator;
		        
		    else : // Else if illustrator doesn't exists redirect to 404 error
		    
		        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
                
            endif;
		    
		else : // Else if illustrator not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Brand action - list resources by brand
     */
    public function brandAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $page = $this->_getParam('page');
    	if($page != NULL) {
    		$this->view->page = $page;
    	} else {
    		$this->view->page = 1;
    	}
    	
    	$brand = urldecode($this->_request->getParam('variable'));
    	
    	if ($brand != NULL) : // If manufacturer set continue
    	
    	    // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $select = $registry->db->select()
    					       ->from(array('b' => 'resources_brands'))
				   		       ->where('b.rbrand_title = ?',$brand)
				   		       ->limit(1,0);
				   		       
		    $brandArray = $registry->db->fetchall($select);
		    
		    // If manufacturer exists continue
		    if (count($brandArray)) :
		    
		        $this->view->brand = $brand;
		        
		        // Pass manufacturer array to View    
		        $this->view->brandArray = $brandArray[0];
		        
    	        $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		           ->join(array('t' => 'resources_types'),'t.rtype_id = r.resource_type')
				   		           ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       		       ->where('r.resource_status = ?','published')
				   	    	       ->where('b.rbrand_title = ?',$brand)
    					           ->order('r.resource_title ASC');

    	        //Create Paginator Object
		        $paginator = Zend_Paginator::factory($select);
		        $paginator->setCurrentPageNumber($this->view->page);
		        $paginator->setItemCountPerPage($registry->resources->rows);
		        $paginator->setPageRange(5);
		
		        //Save Paginator as View var.
		        $this->view->resourcesArray = $paginator;
		        
		    else : // Else if manufacturer doesn't exists redirect to 404 error
		    
		        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
                
            endif;
		    
		else : // Else if manufacturer not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
        
    }
    
	/**
     * Resource action - display article
     */
    public function resourceAction ()
    {    
        $this->setRefer(); // Set auth referer
        
        $this->view->params = $this->_request->getParams(); // Pass params to view
        
        $resourceid = $this->_request->getParam('id'); // Get Article ID from params
        
        if (isset($resourceid)) : // If resource ID set continue
        
		    // Setup Registry
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Get Article data
    	    $select = $registry->db->select()
    					           ->from(array('r' => 'resources'))
				   		           ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		           ->join(array('t' => 'resources_types'),'t.rtype_id = r.resource_type')
				   		           ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       		       ->where('r.resource_status = ?','published')
				   	    	       ->where('r.resource_id = ?',$resourceid)
    					           ->limit(1,0);

    	    // Set the data array
		    $resourceArray = $registry->db->fetchall($select);
		
		    if (count($resourceArray)) : // If resource exists

                // Pass Resource to View    
		        $this->view->resourceArray = $resourceArray[0];
            
            else : // Else redirect to 404 Error
        
    	        $this->_helper->layout->setLayout('main');	    
                $this->_forward('notfound','error','default');
            
            endif;
        
            $this->view->comment = NULL; // Reset the comment value
        
            if($this->_request->isPost()) // If form has been posted
            {
                if ($this->view->authenticated) // The user is authenticated
                {
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
                        if($this->view->resourceArray['resource_moderate'] == 'Y') :
                            $approved = 'N';
                        else :
                            $approved = 'Y';
                        endif;
                    
                        // Create our data array
                        $data = array(
                        	'comment_type'      => 'R',
    						'comment_slave'     => $this->view->resourceArray['resource_id'],
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
                }
            }
        
            // Get Comments
    	    $select = $registry->db->select()
    					       ->from(array('c' => 'comments'),array('c.*'))
				   		       ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role'))
				   		       ->where('comment_approved = ?','Y')
				   		       ->where('comment_type = ?','R')
				   		       ->where('comment_slave = ?',$resourceid)
    					       ->order('c.comment_date ASC');

    	    // Pass the comments to view
		    $this->view->commentsArray = $registry->db->fetchall($select);
		
		else : // Else if Article ID not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
    }
    
	/**
     * Tags action - show resource tag cloud
     */
    public function tagsAction ()
    {    
        // Set as referrer for login
        $this->setRefer();
    }
    
	/**
     * Tag action - list resources with tag
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
				   		   ->join(array('r' => 'resources'),'t.tag_slave = r.resource_id')
    					   ->join(array('c' => 'resources_categories'),'r.resource_category = c.rcat_id',array('c.rcat_title'))
				   		   ->join(array('y' => 'resources_types'),'y.rtype_id = r.resource_type')
				   		   ->join(array('b' => 'resources_brands'),'b.rbrand_id = r.resource_brand',array('b.rbrand_title','b.rbrand_id'))
				       	   ->where('r.resource_status = ?','published')
				   		   ->where('t.tag_tag = ?',$tag)
				   		   ->where('t.tag_type = ?','R')
    					   ->order('r.resource_title ASC');

    	// Create Paginator Object
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->view->page);
		$paginator->setItemCountPerPage($registry->resources->rows);
		$paginator->setPageRange(5);
		
		// Save Paginator as View var.
		$this->view->resourcesArray = $paginator;
    }
    
}