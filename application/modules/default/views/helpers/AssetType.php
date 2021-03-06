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
class Zend_View_Helper_AssetType
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function AssetType($mime) 
    {
        $images = array('image/jpeg', 'image/gif', 'image/png', 'image/bmp'); // Image File Types
        
        $videos = array('video/x-flv'); // Video File Types
        
        if (in_array($mime, $images)) :
            $type = 'image';
        elseif(in_array($mime, $videos)) :
            $type = 'video';
        else :
            $type = 'other';
        endif;
  
        return $type;
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
