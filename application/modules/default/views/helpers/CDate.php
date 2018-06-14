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
class Zend_View_Helper_CDate
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function CDate($date)
    {
        
        $formatted = strtotime($date);
        
        return date('l jS F Y g:ia',$formatted);				   
    	
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
