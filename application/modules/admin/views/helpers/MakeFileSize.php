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
class Zend_View_Helper_MakeFileSize
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function MakeFileSize($bytes, $precision = 2) 
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
  
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
  
        $bytes /= pow(1024, $pow);
  
        return round($bytes, $precision) . ' ' . $units[$pow];
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
