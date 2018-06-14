<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * Find and Format File Size Helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_MakeImageSize
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function MakeImageSize ($file) 
    {
        $size = getimagesize($file);
        
        return $size['0'].'px x '.$size['1'].'px';
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
