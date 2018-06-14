<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ArtCategoryList helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_CSlaveDetails
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function cslaveDetails($slave,$type)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($type == 'A') :
    	
    	    $select = $registry->db->select()
    					       ->from(array('a' => 'articles'),array('a.article_id','a.article_title'))
				   		       ->where('article_id = ?',$slave)
				   		       ->limit(1,0);
    					   
		    $articleArray = $registry->db->fetchall($select);
            $article = $articleArray[0];
            
            return array($this->view->slaveTitle = $article['article_title'],$this->view->slaveID = $article['article_id'],$this->view->slaveType = 'Article');
            
        elseif ($type == 'E') :
    	
    	    $select = $registry->db->select()
    					       ->from(array('e' => 'events'),array('e.event_id','e.event_title'))
				   		       ->where('event_id = ?',$slave)
				   		       ->limit(1,0);
    					   
		    $eventArray = $registry->db->fetchall($select);
            $event = $eventArray[0];
            
            return array($this->view->slaveTitle = $event['event_title'],$this->view->slaveID = $event['event_id'],$this->view->slaveType = 'Event');
            
        elseif ($type == 'R') :
    	
    	    $select = $registry->db->select()
    					       ->from(array('r' => 'resources'),array('r.resource_id','r.resource_title'))
				   		       ->where('resource_id = ?',$slave)
				   		       ->limit(1,0);
    					   
		    $resourceArray = $registry->db->fetchall($select);
            $resource = $resourceArray[0];
            
            return array($this->view->slaveTitle = $resource['resource_title'],$this->view->slaveID = $resource['resource_id'],$this->view->slaveType = 'Resource');
            
        elseif ($type == 'Y') :
        
    	    $select = $registry->db->select()
    					       ->from(array('y' => 'youtube'),array('y.yt_id','y.yt_title'))
				   		       ->where('yt_id = ?',$slave)
				   		       ->limit(1,0);
    					   
		    $videoArray = $registry->db->fetchall($select);
            $video = $videoArray[0];
            
            return array($this->view->slaveTitle = $video['yt_title'],$this->view->slaveID = $video['yt_id'],$this->view->slaveType = 'YouTube');
            
        endif;
    }
    /**
     * Sets the view field 
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
