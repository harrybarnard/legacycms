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
class Zend_View_Helper_YRenderVideo
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function YRenderVideo ($key)
    {
	    $onloadScript = 'vidID = "'.$key.'"';
        $this->view->headScript()->appendFile('http://www.google.com/jsapi','text/javascript')
                                 ->appendFile($this->view->baseUrl().'/_scripts/default/youtube.js','text/javascript')
                                 ->appendScript($onloadScript);
        echo '<div id="videoDiv" class="js-on"><div class="aBox copy cInfo">You must have <a href="http://www.adobe.com/go/getflash" target="_blank">Flash</a> installed to view this video.</div></div>
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
