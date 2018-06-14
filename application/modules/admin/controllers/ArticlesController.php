<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_ArticlesController extends CMS_Controller_Action_Admin 
{
    /**
     * Sets initial vars for controller
     */
    public function init()
    {
    	$this->registry = Zend_Registry::getInstance();
    	$this->_helper->layout->setLayout('admin');	
    }
    
	/**
	 * The default action
	 */
	public function indexAction() 
	{
		$this->_helper->redirector('manage','articles','admin');
	}
	
	/**
	 * Manage action
	 */
	public function manageAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles')) :
	    
		    $this->_request->setParam('items',15);
		    $this->_request->setParam('range',5);
	    
	        $params = $this->_request->getParams();
		
            $articles = new Articles();
            
            $this->view->filter = $articles->getFilter($params);
		    $this->view->articlesArray = $articles->fetchArticles($params);
		    $this->view->categoryArray = $articles->fetchCategories();
		    
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Edit action
	 */
	public function editAction() 
	{
		if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'aarticleedit')) :
		
		    $id = $this->_getParam('id');
		
            $articles = new Articles();
            $this->view->articleArray = $articles->fetchArticle($id);
            
            if(!count($this->view->articleArray)) :
                $this->_helper->redirector('manage','articles','admin');
            endif;
        
        else :
        
		    $this->_forward('privileges','error','admin');
		    
		endif;
	}
	
	/**
	 * Delete action
	 */
	public function deleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'aarticledelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
            
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id) && is_numeric($id)) :
        
                $articles = new Articles();
                $articles->deleteArticle($id);
                            
		        echo '<p class="Spacer"></p>
				<div class="cUpd">Article Deleted</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="articledeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
				</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this article?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="articledeleteButton" value="Delete" iconClass="deletearticleIcon" onClick="getDialog(\'/admin/articles/delete/id/'.$id.'/confirm/1/\',\'Delete Article\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="articledeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	    
	}
	
	/**
	 * Save action
	 */
	public function saveAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'aarticleedit')) :
	    
		    $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
		
		    $id = $this->_getParam('id');
        
            if (isset($id)) :
        
	            $options = array();

                $filters = array();
    
                $validators = array(
                	'title' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
            		'category' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    ),
                	'introduction' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Teaser is required"))
                    ),
                	'content' => array('allowEmpty' => true),
                    'comments' => array('allowEmpty' => true),
                    'moderate' => array('allowEmpty' => true),
                    'sticky' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                if ($input->isValid()) :
            
                    $articles = new Articles();
                    $articles->updateArticle(array('id' => $id,
                                                   'title' => $input->title,
                                                   'category' => $input->category,
                                                   'introduction' => $input->introduction,
                                                   'content' => $input->content,
                                                   'comments' => $input->comments,
                                                   'moderate' => $input->moderate,
                                                   'sticky' => $input->sticky
                                                  ));
                                                  
                   echo '<p class="Spacer"></p>
					<div class="cUpd">Article Saved</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="articlesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="articlesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cErr">Article Not Specified!</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="articlesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Publish action
	 */
	public function publishAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'aarticlepublish')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
	    
		    $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
        
	            $options = array();

                $filters = array();
    
                $validators = array(
                	'title' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
            		'category' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    ),
                	'introduction' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Teaser is required"))
                    ),
                	'content' => array('allowEmpty' => true),
                    'comments' => array('allowEmpty' => true),
                    'moderate' => array('allowEmpty' => true),
                    'sticky' => array('allowEmpty' => true)
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
           
                if ($input->isValid()) :
                
                    $articles = new Articles();
                    $articles->updateArticleStatus(array('id' => $id,
                    									 'status' => 'published'));
                    $articles->updateArticle(array('id' => $id,
                                                   'title' => $input->title,
                                                   'category' => $input->category,
                                                   'introduction' => $input->introduction,
                                                   'content' => $input->content,
                                                   'comments' => $input->comments,
                                                   'moderate' => $input->moderate,
                                                   'sticky' => $input->sticky
                                                  ));
                   
                    echo '<p class="Spacer"></p>
				    <div class="cUpd">Article Published</div>
				    <p class="Spacer"></p>
				    <div class="cFormDS">
					    <button dojoType="dijit.form.Button" type="button" id="articlepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				    </div>';
                    
                else :
            
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    $this->_helper->messages->render($this->messages);
                    echo '<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="articlepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                        
                endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to publish this article?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="articlepublishButton" value="Publish Now" iconClass="publishIcon" onClick="postDialog(\'/admin/articles/publish/id/'.$id.'/confirm/1/\',\'editForm\',\'Publish Article\',\'1\');">Publish Now</button>
					<button dojoType="dijit.form.Button" type="button" id="articlepublishcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
	}
	
	/**
	 * Details action - display article details
	 */
	public function detailsAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'aarticleedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
	        
	        $articles = new Articles();
            $this->view->articleArray = $articles->fetchArticle($id);
		
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * New action - create a new article
	 */
	public function newAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'aarticlenew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'articletitle' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"))
                    ),
            		'articlecategory' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Category is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    $articles = new Articles();
                    
                    $articles->newArticle(array('title' => $input->articletitle,
                                                'category' => $input->articlecategory,
                                                'author' => $this->view->user->user_id
                                               ));
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Article Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="articlenewcontinueButton" value="Continue" iconClass="continueIcon" onClick="goTo(\'/admin/articles/edit/id/'.$this->registry->db->lastInsertId().'\');">Continue</button>
					</div>';
                
                 else :
            
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->articletitle = $input->articletitle;
                    $this->view->articlecategory = $input->articlecategory;
             
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
     * Delete Category Action - Remove an article category and move associated articles to category default
     */
    public function categorydeleteAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'acategorydelete')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $confirm = $this->_getParam('confirm');
            $id = $this->_getParam('id');
        
            if ($confirm == '1' & isset($id)) :
            
                if($id != '1') :
        
                    $articles = new Articles();
                    $articles->deleteCategory($id);
            
		            echo '<p class="Spacer"></p>
					<div class="cUpd">Category Deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorydeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		            
		        else :
		            
		            echo '<p class="Spacer"></p>
					<div class="cUpd">This category cannot be deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorydeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		            
		        endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
				<div class="cUpd">Are you sure you want to delete this category?</div>
				<p class="Spacer"></p>
				<div class="cFormDS">
					<button dojoType="dijit.form.Button" type="button" id="categorydeleteButton" value="Delete" iconClass="deletecategoryIcon" onClick="getDialog(\'/admin/articles/categorydelete/id/'.$id.'/confirm/1/\',\'Delete Category\');">Delete</button>
					<button dojoType="dijit.form.Button" type="button" id="categorydeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
				</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
	 * New category action - create a new category
	 */
	public function categorynewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'acategorynew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                    'categorytitle' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists('articles_categories','acat_title'),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Category Already Exists"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    $articles = new Articles();
                    $articles->newCategory(array('title' => $input->categorytitle));
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Category Created</div>
					<p class="Spacer"></p>
                    <div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="categorynewcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
                    
                 else :
             
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->categorytitle = $this->_getParam('categorytitle');
            
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	    
	}
	
	/**
	 * Category action - edit category
	 */
	public function categoryAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'aarticles') & $this->view->acl->isAllowed($this->view->user->user_role, 'acategoryedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $id = $this->_getParam('id');
	        
	        $articles = new Articles();
	        $category = $articles->fetchCategory($id);
	    
            $this->view->categorytitle = $category['acat_title'];
            $this->view->categoryid = $id;

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                    'categorytitle' => array(
                		'presence' => 'required',
                 		'NotEmpty',
                        new Zend_Validate_Alnum(true),
                        new Zend_Validate_Db_NoRecordExists(array('table' => 'articles_categories','field' => 'acat_title','exclude' => array('field' => 'acat_id','value' => $id))),
                    	'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Title is required"),array( Zend_Validate_Alnum::NOT_ALNUM => "Invalid Title"),array(Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND => "Category already exists"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
                    $articles->updateCategory(array('id' => $id,
                                                    'title' => $input->categorytitle
                                                   ));
                    
                    echo '<p class="Spacer"></p>
						<div class="cUpd">Category Saved</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="categoryeditcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
                    
                 else :
             
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->categorytitle = $this->_getParam('categorytitle');
            
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	    
	}
	
}