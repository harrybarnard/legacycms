<?php
/**
 *
 * @author Harry
 * @version 
 */
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Controller/Action/Helper/Abstract.php';
/**
 * SearchIndex Action Helper 
 * 
 * @uses actionHelper Zend_Controller_Action_Helper
 */
class Zend_Controller_Action_Helper_SearchIndex extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    public $pluginLoader;
    
    public function stubTrim ($str, $maxlen=140, $elli=NULL, $maxoverflow=15)
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
    
    public function delete ($key)
    {
        // setup database
  	    $registry = Zend_Registry::getInstance();
        
        // Create index
        $index = Zend_Search_Lucene::open($registry->search->search->syspath.'/site-index');
        
        $hits = $index->find('key:' . $key);
        foreach ($hits as $hit) {
            $index->delete($hit->id);
        }
    }
    
    public function add ($params)
    {
        // setup database
  	    $registry = Zend_Registry::getInstance();
        
        // Create index
        $index = Zend_Search_Lucene::open($registry->search->search->syspath.'/site-index');

        $doc = new Zend_Search_Lucene_Document();
        
        $doc->addField(Zend_Search_Lucene_Field::Keyword('key', $params['key']));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('date', $params['date']));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $params['title']));
        $doc->addField(Zend_Search_Lucene_Field::Text('details', $params['details']));
        $doc->addField(Zend_Search_Lucene_Field::Text('stub', $this->stubTrim($params['stub'])));
        $doc->addField(Zend_Search_Lucene_Field::Text('url', $params['url']));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('contents', $params['contents']));
        $index->addDocument($doc);
    }
    
    public function update ($params)
    {
        // Setup Registry
  	    $registry = Zend_Registry::getInstance();
  	    
  	    // Delete Existing Document Entry
  	    $this->delete($params['key']);
        
        // Create index
        $index = Zend_Search_Lucene::open($registry->search->search->syspath.'/site-index');
        
        $doc = new Zend_Search_Lucene_Document();
        
        $doc->addField(Zend_Search_Lucene_Field::Keyword('key', $params['key']));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('date', $params['date']));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $params['title']));
        $doc->addField(Zend_Search_Lucene_Field::Text('details', $params['details']));
        $doc->addField(Zend_Search_Lucene_Field::Text('stub', $this->stubTrim($params['stub'])));
        $doc->addField(Zend_Search_Lucene_Field::Text('url', $params['url']));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('contents', $params['contents']));
        $index->addDocument($doc);
    }
    
    public function create ()
    {
        // setup database
  	    $registry = Zend_Registry::getInstance();
        
        // Create index
        $index = Zend_Search_Lucene::create($registry->search->search->syspath.'/site-index');
    }
}

