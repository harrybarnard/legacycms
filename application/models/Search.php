<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Search
{
    /**
     * Constructor
     * @return void
     */
    public function __construct ()
    {
        $this->registry = Zend_Registry::getInstance();
    }
    
    /**
     *  Process string for search stub
     *  @param string $str The string to process
     *  @param integer $maxlen The max chars for output
     *  @param string $elli The terminating chars
     *  @param integer $maxoverflow Maximum chars to overflow by
     */
    private function stubTrim ($str, $maxlen=140, $elli=NULL, $maxoverflow=15)
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
     *  Create a search index entry
     *  @param array $params Params for entry
     */
    private function createEntry($params)
    {
        $index = Zend_Search_Lucene::open($this->registry->search->search->syspath.'/site-index');

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
    
    /**
     *  Remove search index entry
     *  @param string $key The key of entry
     */
    public function deleteEntry($key)
    {
        if ($key != NULL) :
        
            $index = Zend_Search_Lucene::open($this->registry->search->search->syspath.'/site-index');
        
            $hits = $index->find('key:' . $key);
            foreach ($hits as $hit) {
                $index->delete($hit->id);
            }
		    
		else :
		    
		    throw new Exception('Invalid entry key');
		    
        endif;
    }
    
    /**
     *  Add new search index entry
     *  @param array $params The params for entry
     */
    public function newEntry ($params)
    {
        if(is_array($params)) :
            $this->createEntry($params);
        else :
            throw new Exception('Invalid parameters');
        endif;
    }
    
    /**
     *  Update existing search index entry
     *  @param array $params The new params for entry
     */
    public function updateEntry ($params)
    {
        if(is_array($params)) :
            $this->deleteEntry($params['key']);
            $this->createEntry($params);
        else :
            throw new Exception('Invalid parameters');
        endif;
    }
    
    /**
     *  Create initial site search index
     */
    public function createSiteIndex ()
    {
        $index = Zend_Search_Lucene::create($this->registry->search->search->syspath.'/site-index');
    }
    
}
?>