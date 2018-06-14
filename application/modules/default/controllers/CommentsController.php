<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class CommentsController extends CMS_Controller_Action_Default
{
    public function init()
    {
    }
    
	/**
     * Comments action - display comments
     */
    public function commentsAction ()
    {    
        $this->_helper->layout->disableLayout(); // Disable Layouts
                
        $this->_request->setParam('items',10);
		$this->_request->setParam('range',5);
		$this->_request->setParam('status','Y');
	    
	    $params = $this->_request->getParams();
        
        $comments = new Comments();
        $this->view->commentsArray = $comments->fetchComments($params); 
        
    }
    
}