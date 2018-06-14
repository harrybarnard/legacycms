<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class YouTube3
{
	/**
     * Creates a custom formatted date
     * @param string $format Format to apply
     * @param string $date Date to be formatted
     */
    private function makeDate($format,$date = NULL)
    {
        if($date != NULL) :
            $formatted = strtotime($date);
            return date($format,$formatted);
        else :
            return date($format);
        endif;
    }
	
	/**
     * Constructor
     * @return void
     */
    public function __construct()
    {
        $this->registry = Zend_Registry::getInstance();
        
        $youtube = new Zend_Config_Ini('../application/configs/youtube.ini', array('config'));
        $this->registry->set('youtube', $youtube);
    }
	
	/**
     *  Creates an array of valid filter params
     *  @param array $params Array of params to add to filter
     *  @return array $filter Array of valid filter params
     */
    public function getFilter($params)
	{
	    $filter = array();
	    
        if (isset($params['sort'])) :
            $filter['sort'] = $params['sort'];
        else :
            $filter['sort'] = 'published';
        endif;
        
	    return $filter;
	}
	
	/**
     *  Syncs video object with database 
     *  @param object $video Video object to sync
     */
	public function getFeed($feed)
	{
        $frontendOptions = array(
            'lifetime' => 86400,
            'automatic_serialization' => true
        );
        $backendOptions = array('cache_dir' => '../cache/');
        $cache = Zend_Cache::factory(
            'Core', 'File', $frontendOptions, $backendOptions
        );
       
        Zend_Feed_Reader::setCache($cache);
        Zend_Feed_Reader::useHttpConditionalGet();
       
        return $feed = Zend_Feed_Reader::import($feed);
	}
	
	
    /**
     *  Updates video 
     *  @param array $params Video params
     */
	public function updateVideo($params)
	{
         if ($params['comments'] != 'Y') :
             $comments = 'N';
         else :
             $comments = 'Y';
         endif;
            
         if ($params['moderate'] != 'Y') :
             $moderate = 'N';
         else :
             $moderate = 'Y';
         endif;
            
         $data = array(
            'yt_comments'	=> $comments,
            'yt_moderate'	=> $moderate,
         );

         $this->registry->db->update('youtube', $data, 'yt_id = '.$params['id']);
	}
	
    /**
     *  Delete video
     *  @param integer $id Video id
     */
	public function deleteVideo($id)
	{
	    if (isset($id) && is_numeric($id)) :
	    
            $comments = new Comments();
            $comments->deleteSlaveComment('Y',$id);	
        
            $this->registry->db->delete('youtube', 'yt_id = '.$id);
        else :
            throw new Exception('Invalid video id');
        endif;
	}
	
}
?>