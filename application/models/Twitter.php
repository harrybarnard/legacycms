<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Twitter
{
	/**
     * Constructor
     * @return void
     */
    public function __construct()
    {
        $this->registry = Zend_Registry::getInstance();
        
        $twitter = new Zend_Config_Ini('../application/configs/twitter.ini', array('config'));
        $this->registry->set('twitter', $twitter);
    }
	
	public function fetchTweets($params = null)
	{
	    if(isset($params['count'])) :
	        $count = $params['count'];
	    else :
	        $count = '10';
	    endif;
	    
        return $feed = Zend_Feed_Reader::import($this->registry->twitter->feed);
	}
	
}
?>