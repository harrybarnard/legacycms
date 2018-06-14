<?php
/**
 *
 * @author Harry
 * @version 
 */
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Controller/Action/Helper/Abstract.php';
/**
 * MakeStub Action Helper 
 * 
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_Strings extends Zend_Controller_Action_Helper_Abstract
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
    public function makeStub($str, $maxlen=140, $elli=NULL, $maxoverflow=15)
    {    
       global $CONF;
       
       $string = strip_tags($str);
      
       if (strlen($string) > $maxlen) {
          
           if ($CONF["BODY_TRIM_METHOD_STRLEN"]) {
               return substr($string, 0, $maxlen);
           }
           
           $elli = ' [&#8230;]';
          
           $output = NULL;
           $body = explode(" ", $string);
           $body_count = count($body);
      
           $i=0;
  
           do {
               $output .= $body[$i]." ";
               $thisLen = strlen($output);
               $cycle = ($thisLen < $maxlen && $i < $body_count-1 && ($thisLen+strlen($body[$i+1])) < $maxlen+$maxoverflow?true:false);
               $i++;
           } while ($cycle);
           return $output.$elli;
       }
       else return $string;
    }
}

