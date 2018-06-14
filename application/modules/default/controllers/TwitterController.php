<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class TwitterController extends CMS_Controller_Action_Default
{
    public function init()
    {
        $this->registry = Zend_Registry::getInstance();
        
    	$this->_helper->layout->disableLayout();
    }
    
	/**
     * Tweets action - list recent tweets
     */
    public function tweetsAction ()
    {    
		$params = $this->_request->getParams();
		
		$twitter = new Twitter();
        
        $this->view->tweetsArray = $twitter->fetchTweets();    
    }
    
}