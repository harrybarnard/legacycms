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
class Zend_View_Helper_EDate
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function EDate($startdate,$enddate)
    {
        
        $fstart = strtotime($startdate); // convert start date to timestamp
        $start = date('l jS F Y g:ia',$fstart); // create formatted start date from timestamp 
        
        if($enddate != NULL) : // If and end date is set
            $sstart = date('dmY',$fstart); // create start date string for comparison
        
            $fend = strtotime($enddate); // convert end date to timestamp
            $send = date('dmY',$fend); // create end date for comparison
        
            if($sstart == $send) : // If the start and end dates match
                $end = ' - '.date('g:ia',$fend); // create formatted end time
            else :
                $end = ' - '.date('l jS F Y g:ia',$fend); // create formatted end date
            endif;
        
            return $start.$end;
            
        else : // If end date is not set
        
            return $start;
           
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
