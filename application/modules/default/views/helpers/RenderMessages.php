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
class Zend_View_Helper_RenderMessages
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function RenderMessages ($messages)
    {
	    if (isset($messages)) :
            foreach ($messages as $field => $messages) :
                foreach ($messages as $message) :
                    if(is_array($message)) :
                        foreach ($message as $mess) :
                            echo '<div class="aBox cErr">'.$mess.'</div>';
                        endforeach;
                    else :
                       echo '<div class="aBox cErr">'.$message.'</div>';
                    endif; 
                endforeach;
             endforeach;
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
