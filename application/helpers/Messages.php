<?php
/**
 *
 * @author Harry
 * @version 
 */
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Controller/Action/Helper/Abstract.php';
/**
 * Messages Action Helper 
 * 
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_Messages extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;
    /**
     * Constructor: initialize plugin loader
     * 
     * @return void
     */
    public function __construct ()
    {
        // TODO Auto-generated Constructor
        $this->pluginLoader = new Zend_Loader_PluginLoader();
    }
    /**
     * Strategy pattern: call helper as broker method
     */
    public function direct ()
    {    // TODO Auto-generated 'direct' method
    }
	/**
     * Strategy pattern: call helper as broker method
     */
    public function render ($messages)
    {
        if (isset($messages)) :
            echo '<p class="Spacer"></p>';
            foreach ($messages as $field => $messages) :
                foreach ($messages as $message) :
                    if(is_array($message)) :
                        foreach ($message as $mess) :
                            echo '<div class="cErr">'.$mess.'</div>';
                        endforeach;
                    else :
                       echo '<div class="cErr">'.$message.'</div>';
                    endif; 
                endforeach;
             endforeach;
         endif;
    }
}

