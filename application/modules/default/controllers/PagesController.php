<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class PagesController extends CMS_Controller_Action_Default
{
    
    public function init()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load pages configuration
        $pages = new Zend_Config_Ini('../application/configs/pages.ini', array('config'));
        $registry->set('pages', $pages);
        
        // Change to pages layout
    	$this->_helper->layout->setLayout('pages');
    }

	/**
     * Page action - display page
     */
    public function pageAction ()
    {    
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
        $pageslug = $this->_request->getParam('slug');
        
		// setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// get article data
    	$select = $registry->db->select()
    					   ->from(array('p' => 'pages'))
				   		   ->where('page_status = ?','published')
				   		   ->where('page_slug = ?',$pageslug)
    					   ->limit(1,0);

    	// Set the data array
		$pageArray = $registry->db->fetchall($select);
		
		if (count($pageArray)) :

            $this->view->pageArray = $pageArray[0];
            
        else :
        
    	    $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;
    }
    
	/**
     * Tags action - list all pages tags
     */
    public function tagsAction ()
    {
    	$this->setRefer();
    }
    
	/**
     * Tag action - list pages with tag
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
    	
    	$tag = urldecode($this->_request->getParam('tag'));
    	$this->view->tag = $tag;
        
		// Get Registry
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('t' => 'tags'),array('t.*'))
				   		   ->join(array('p' => 'pages'),'t.tag_slave = p.page_id',array('p.*'))
				   		   ->where('page_status = ?','published')
				   		   ->where('t.tag_tag = ?',$tag)
				   		   ->where('t.tag_type = ?','P')
    					   ->order('p.page_title ASC');

    	// Create Paginator Object
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($this->view->page);
		$paginator->setItemCountPerPage($registry->pages->rows);
		$paginator->setPageRange(5);
		
		//Save Paginator as View var.
		$this->view->pagesArray = $paginator;
    }
    
}