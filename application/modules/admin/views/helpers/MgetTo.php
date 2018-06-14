<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * CatSelect helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_MgetTo
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function MgetTo($to,$type)
    {
    	$registry = Zend_Registry::getInstance();
    	
    	if ($type == 'G') :
    	
	        $select = $registry->db->select()
    					           ->from(array('g' => 'mail_groups'))
    					           ->where('g.mgroup_id = ?', $to);
    					           
    		$toArray = $registry->db->fetchall($select);
		
            $toArray = $toArray[0];
        
            return '<a href="'.$this->view->url(array('action' => 'manage','page' => NULL,'group' => $toArray['mgroup_id'])).'" title="Add to Filter">List: '.$toArray['mgroup_title'];
            
        elseif ($type == 'R') :
        
            $select = $registry->db->select()
    					           ->from(array('r' => 'users_roles'))
    					           ->where('r.role_id = ?', $to);
    					           
    		$toArray = $registry->db->fetchall($select);
		
            $toArray = $toArray[0];
        
            return '<a href="'.$this->view->url(array('action' => 'manage','page' => NULL,'role' => $toArray['role_id'])).'" title="Add to Filter">Role: '.$toArray['role_title'];
            
        elseif ($type == 'U') :
        
            $select = $registry->db->select()
    					           ->from(array('u' => 'users'))
    					           ->where('u.user_id = ?', $to);
    					           
    		$toArray = $registry->db->fetchall($select);
		
            $toArray = $toArray[0];
        
            return '<a href="'.$this->view->url(array('action' => 'manage','page' => NULL,'user' => $toArray['user_id'])).'" title="Add to Filter">User: '.$toArray['user_alias'];
            
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
