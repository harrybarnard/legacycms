<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * EDate helper - Create a formatted event date
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ECDate
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function ECDate($startdate)
    {
        
        $fstart = strtotime($startdate); // convert start date to timestamp
        $start = date('h:ia',$fstart); // create formatted start date from timestamp 
        
        return $start;
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
