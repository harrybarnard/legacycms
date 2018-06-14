<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * EDate helper - Create a formatted short event date
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_TwitTime
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    function twitTime($start,$end){
	    //both times must be in seconds
	    $time = $end - $start;
	    if(60 < $time && $time <= 3600){
	        $result = round($time/60,0);
	        //if($result == 1) {
		        //return 'about '.$result.' minute ago';
	        //} else {
	            return 'about '.$result.' minutes ago';
	        //}
	    }
	    if(3600 < $time && $time <= 86400){
	        $result = round($time/3600,0);
	        //if($result == 1) {
		        //return 'about '.$result.' hour ago';
	        //} else {
	            return 'about '.$result.' hours ago';
	        //}
	    }
	    if($time > 86400){
		    return date('l jS F Y g:ia',$start);
	    }
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
