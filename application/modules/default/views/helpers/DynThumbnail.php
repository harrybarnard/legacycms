<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
require_once 'PHPThumb/ThumbLib.inc.php';
/**
 * DynThumbnail helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_DynThumbnail
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function dynThumbnail ($file,$w,$h,$type = 'adaptive')
    {
        $registry = Zend_Registry::getInstance();
        
        // Set system path to images folder
        $syspath = $registry->assets->assets->syspath.'/images/';
        
        $defaultfile = $syspath.'default.png';
        
        $imagefile = $syspath.$file;
        
        // If the image file doesn't exist
        if(!file_exists($imagefile)) :
        
            if($type == 'adaptive') :
        
                // Set full path to thumbnail folder
                $thumbdefault = $syspath.'_thumbs/'.$w.'x'.$h.'default.png';
        
                // If the thumb doesn't exist create it
                if (!file_exists($thumbdefault)) :
        
                    $thumb = PhpThumbFactory::create($defaultfile);

                    $thumb->adaptiveResize($w, $h);
                    $thumb->save($thumbdefault);
                    
                endif;
                    
                // Output thumb path
                echo '/_assets/images/_thumbs/'.$w.'x'.$h.'default.png';

            elseif ($type == 'resize') :
            
                // Set full path to thumbnail folder
                $thumbdefault = $syspath.'_resize/'.$w.'x'.$h.'default.png';
        
                // If the thumb doesn't exist create it
                if (!file_exists($thumbdefault)) :
        
                    $thumb = PhpThumbFactory::create($defaultfile);

                    $thumb->resize($w, $h);
                    $thumb->save($thumbdefault);
                    
                endif;
                
                // Output thumb path
                echo '/_assets/images/_resize/'.$w.'x'.$h.'default.png';
            
            endif;
        

        else :
        
            if($type == 'adaptive') :
            
                // Set full path to thumbnail folder
                $thumbfile = $syspath.'_thumbs/'.$w.'x'.$h.$file;
        
                // If the thumb doesn't exist create it
                if (!file_exists($thumbfile)) :
        
                    $thumb = PhpThumbFactory::create($syspath.$file);

                    $thumb->adaptiveResize($w, $h);
                    $thumb->save($thumbfile);
            
                endif;
        
                // Output thumb path
                echo '/_assets/images/_thumbs/'.$w.'x'.$h.$file;
                
            elseif ($type == 'resize') :
            
                // Set full path to thumbnail folder
                $thumbfile = $syspath.'_thumbs/'.$w.'x'.$h.$file;
        
                // If the thumb doesn't exist create it
                if (!file_exists($thumbfile)) :
        
                    $thumb = PhpThumbFactory::create($syspath.$file);

                    $thumb->resize($w, $h);
                    $thumb->save($thumbfile);
            
                endif;
        
                // Output thumb path
                echo '/_assets/images/_resize/'.$w.'x'.$h.$file;
                
            endif;
            
        endif;
        
        return null;
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
