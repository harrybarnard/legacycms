<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * RTagCount helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_MakeDate
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function MakeDate($format,$date)
    {
        
        $formatted = strtotime($date);
        
        return date($format,$formatted);	   
    	
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
