<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * TagCloud helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_MSubStatusAll
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function MSubStatusAll($list,$user)
    {
    	if(isset($list) & isset($user)) :
    	    if(is_numeric($list) & is_numeric($user)) :
    	        // Get Registry
    	        $registry = Zend_Registry::getInstance();
    	
    	        $select = $registry->db->select()
    		    	               ->from(array('s' => 'mail_subscriptions'))
					               ->where('s.msub_group = ?',$list)
					               ->where('s.msub_user = ?',$user);
    					   
    	        $subscriptionArray = $registry->db->fetchall($select);
    	
                if(count($subscriptionArray) > 0) :
                    return true;
                else :
                    return false;
                endif;
            else :
                Throw new Exception('Variables must be numeric');
            endif;
        else :
            Throw new Exception('Variables not set');
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
