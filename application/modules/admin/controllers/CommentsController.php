<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_CommentsController extends CMS_Controller_Action_Admin
{
    
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}
	
    public function getAuthor($authorid)
	{
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
	    $select = $registry->db->select()
    					       ->from(array('u' => 'users'))
    					       ->where('u.user_id = ?', $authorid);

		$authorArray = $registry->db->fetchall($select);
		
		if (count($authorArray) > 0) :

            $authorArray = $authorArray[0];
        
            $authorname = $authorArray['user_alias'];
        
            return $this->view->author = $authorname;
            
        else :
        
            return false;
            
        endif;
	}
	
	/**
	 * The default action
	 */
	public function indexAction() {
		$this->_helper->redirector('manage','comments','admin');
	}

	/**
     * Manage Action
     */
    public function manageAction ()
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'gcomments')) :
        
            $this->setLayout();
        
            $type = $this->_request->getParam('type');
		
		    $slave = $this->_request->getParam('slave');
		
		    $author = $this->_request->getParam('author');
		
            $page = $this->_getParam('page');
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
		
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    if ($type != NULL & $slave != NULL & $author != NULL) :
    	        $this->getAuthor($author);
    	        $this->view->type = $type;
    	        $this->view->slave = $slave;
    	        $select = $registry->db->select()
    	                               ->from(array('c' => 'comments'))
    	                               ->where('c.comment_user = ?', $author)
    	                               ->where('c.comment_type = ?', $type)
    	                               ->where('c.comment_slave = ?', $slave)
    	                               ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role','u.user_id'))
    	                               ->order('c.comment_date DESC');
    	    elseif ($type == NULL & $slave == NULL & $author != NULL) :
    	        $this->getAuthor($author);
    	        $select = $registry->db->select()
    	                               ->from(array('c' => 'comments'))
    	                               ->where('c.comment_user = ?', $author)
    	                               ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role','u.user_id'))
    	                               ->order('c.comment_date DESC');
    	    elseif ($type != NULL & $slave != NULL & $author == NULL) :
    	        $this->view->type = $type;
    	        $this->view->slave = $slave;
    	        $select = $registry->db->select()
    	                               ->from(array('c' => 'comments'))
    	                               ->where('c.comment_type = ?', $this->view->type)
    	                               ->where('c.comment_slave = ?', $this->view->slave)
    	                               ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role','u.user_id'))
    	                               ->order('c.comment_date DESC');
    	    else :
    	        $select = $registry->db->select()
    	                           ->from(array('c' => 'comments'))
    	                           ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role','u.user_id'))
    	                           ->order('c.comment_date DESC');
    	    endif;

		    //Create Paginator Object
		    $paginator = Zend_Paginator::factory($select);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(15);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->commentArray = $paginator;
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    	
    }
    
	/**
     * Show Action - Render a list of comments
     */
    public function showAction ()
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'gcomments')) :
        
            $this->_helper->layout->disableLayout(); // Disable Layouts
        
            $slave = $this->_getParam('slave');
            $type = $this->_getParam('type');
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $this->view->type = $type;
    	        
            // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('c' => 'comments'))
    	                           ->where('c.comment_type = ?', $type)
    	                           ->where('c.comment_slave = ?', $slave)
    	                           ->join(array('u' => 'users'),'c.comment_user = u.user_id',array('u.user_alias','u.user_role','u.user_id'))
    	                           ->order('c.comment_date DESC');
    	                           
		    // Set the data array
		    $this->view->commentArray = $registry->db->fetchall($select);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Delete Action - remove a comment from the database
     */
    public function deleteAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'gcomments') & $this->view->acl->isAllowed($this->view->user->user_role, 'gcommentdelete')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $id = $this->_getParam('id');
            $ajax = $this->_getParam('ajax');
        
            if (isset($id)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	
		        // Delete the row
		        $registry->db->delete('comments', 'comment_id = '.$id);
		    
		        echo '<p class="Spacer"></p>
                  	<div class="cUpd">Comment deleted</div>
                 	<p class="Spacer"></p>';
		    
		        if ($ajax != 1) :
				    echo '<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="commentdeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
			    else :
			        echo '<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="commentdeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
			    endif;
		    
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Approve Action - change a comment's status to approved
     */
    public function approveAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'gcomments') & $this->view->acl->isAllowed($this->view->user->user_role, 'gcommentsstatus')) :
        
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $id = $this->_getParam('id');
            $status = $this->_getParam('status');
            $ajax = $this->_getParam('ajax');
        
            if (isset($id) & isset($status)) :
        
                if ($status == 'Y') :
        
                    // setup database
    	            $registry = Zend_Registry::getInstance();
    	
		            // Create our data array
                    $data = array(
            			'comment_approved'	=> 'Y'
                    );

                    // Insert data into database
                    $registry->db->update('comments', $data, 'comment_id = '.$id);
                    
		            echo '<p class="Spacer"></p>
                  		<div class="cUpd">Comment approved</div>
                  		<p class="Spacer"></p>';
                  	
                  	    if ($ajax != 1) :
					        echo '<div class="cFormDS">
									<button dojoType="dijit.form.Button" type="button" id="commentapprovecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
								</div>';
					    else :
					        echo '<div class="cFormDS">
									<button dojoType="dijit.form.Button" type="button" id="commentapprovecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
								</div>';
					    endif;
		        
		        elseif ($status == 'N') :
		        
		            // setup database
    	            $registry = Zend_Registry::getInstance();
    	
		            // Create our data array
                    $data = array(
            			'comment_approved'	=> 'N'
                    );

                    // Insert data into database
                    $registry->db->update('comments', $data, 'comment_id = '.$id);
                    
		            echo '<p class="Spacer"></p>
                  		<div class="cUpd">Comment disapproved</div>
                  		<p class="Spacer"></p>';
		        
				        if ($ajax != 1) :
					        echo '<div class="cFormDS">
									<button dojoType="dijit.form.Button" type="button" id="commentapprovecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
								</div>';
					    else :
					        echo '<div class="cFormDS">
									<button dojoType="dijit.form.Button" type="button" id="commentapprovecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
								</div>';
					    endif;
		        
		        endif;
		    
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
        
    }
    
	/**
     * Comment Tab Action - count comments and update tab title
     */
    public function tabAction () 
    {   
        if($this->view->acl->isAllowed($this->view->user->user_role, 'gcomments')) :
         
            $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    $type = $this->_request->getParam('type');
    	    $slave = $this->_request->getParam('slave');
    	
    	    if ($type == 'U') :
    	
    	        $select = $registry->db->select()
    					           ->from(array('c' => 'comments'))
				   		           ->where('comment_user = ?',$slave);
				   		       
	        else :
	    
	            $select = $registry->db->select()
    					           ->from(array('c' => 'comments'))
				   		           ->where('comment_slave = ?',$slave)
				   		           ->where('comment_type = ?',$type);
				   		       
	        endif;
    					   
    	    $commentArray = $registry->db->fetchall($select);
    	
    	    echo 'Comments ('.count($commentArray).')';
    	
    	else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    }
}