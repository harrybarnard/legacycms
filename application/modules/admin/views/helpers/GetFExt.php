<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * GetFExt helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_GetFExt
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function getFExt ($file)
    {
        $Filename = $file;
        $FileExtension = strrpos($Filename, ".", 1) + 1;
        if ($FileExtension != false)
            return strtolower(substr($Filename, $FileExtension, strlen($Filename) - $FileExtension));
        else
            return "";
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
