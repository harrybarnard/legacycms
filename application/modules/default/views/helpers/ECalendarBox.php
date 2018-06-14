<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ECalendarBox helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ECalendarBox
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function ECalendarBox ($links = false)
    {
        echo '<div class="rBox js-on">
			<div dojoType="dijit.layout.ContentPane" id="calendarPane" preload="true" href="'.$this->view->baseUrl().'/events/acalendar/" style="overflow: hidden;" loadingMessage="<span class=\'dijitContentPaneLoading\'></span>"></div>';
		
		if($links == true) :	
            echo '<div class="rMore" style="margin-top: 5px;">
				<a href="'.$this->view->baseUrl().'/events/map/">Events Map &raquo;</a> <a href="'.$this->view->baseUrl().'/events/">More Events &raquo;</a>
				</div>';
        endif;
				    		
		echo '</div>
			<p class="Spacer js-on"></p>';
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
