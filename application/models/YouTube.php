<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class YouTube
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
        $this->_cache = Zend_Registry::get('cache');
        
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
     *  Fetches YouTube videos based on filter params and returns a video feed object 
     *  @param array $params Array of params to filter by
     *  @return object $videos Videos feed object
     */
	private function feedVideos($params)
	{
	    $cache_id = 'youtube_' . $this->registry->youtube->user;

        if(!($videos = $this->_cache->load($cache_id)))
        {
	    
            $yt = new Zend_Gdata_YouTube();
            $yt->setMajorProtocolVersion(2);
        
            if(isset($params['page'])) :
                $offset = ($params['items'] * ($params['page'] - 1)) + 1;
            endif;
         
            $query = $yt->newVideoQuery();
            $query->setAuthor($params['author']);
            $query->setOrderBy($params['order']);
        
            $videos = $yt->getVideoFeed($query);
            
            $this->_cache->save($videos, $cache_id, array('youtube'), '10800');
            
            $this->syncFeeds($videos);
            
        }
        
        return $videos;
	}
	
	/**
     *  Fetches YouTube video based on key and returns an video array 
     *  @key array $params Array of params to filter by
     *  @return array $videos Video array
     */
	private function feedVideo($key)
	{
	    $cache_id = 'youtube_video_' . md5($key);

        if(!($video = $this->_cache->load($cache_id)))
        {
	        $yt = new Zend_Gdata_YouTube();
            $yt->setMajorProtocolVersion(2);
	    
            $video = $yt->getVideoEntry($key);
            
            $this->_cache->save($video, $cache_id, array('youtube'), '86400');
            
        }
        
        return $video;
	}
	
	/**
     *  Syncs video object with database 
     *  @param object $video Video object to sync
     */
	private function syncFeed($video)
	{
        $id = $video->getVideoId();
        
        $select = $this->registry->db->select()->from(array('y' => 'youtube'))
    	                                       ->where('y.yt_key = ?', $id)
    	                                       ->limit(1,0);

		$entry = $this->registry->db->fetchall($select);
	    
        $vidThumb = $video->getVideoThumbnails();
        
        $author = $video->author[0]->name->text;
        
        $data = array(
        	'yt_key' => $id,
            'yt_title' => $video->getVideoTitle(),
            'yt_author' => $author,
            'yt_description' => $video->getVideoDescription(),
            'yt_date' => $video->getPublished(),
            'yt_duration' => $video->getVideoDuration(),
            'yt_thumb' => $vidThumb[0]['url'],
            'yt_sync' => 'Y'
        );
        
        if(count($entry)) :
            $this->registry->db->update('youtube', $data, 'yt_key = "'.$id.'"');
        else :
	        $this->registry->db->insert('youtube', $data);  
	    endif;
	}
	
	/**
     *  Syncs videos object with database 
     *  @param object $videos Videos object to sync
     */
	private function syncFeeds($videos)
	{
        foreach ($videos as $video) :
            $this->syncFeed($video);
        endforeach;   
	}
	
	public function fetchVideosFeed($params)
	{
	    if(!isset($params['page'])) :
            $params['page'] = 1;
        endif;
        
        if(!isset($params['sort'])) :
            $params['sort'] = 'published';
        endif;
        
        if(!isset($params['type'])) :
            $params['type'] = 'synched';
        endif;
        
	    try { $videos = $this->feedVideos(array('author' => $params['author'],
	                            		        'order' => $params['sort'],
	                                            'items' => $params['items'],
	                                            'page' => $params['page']
	                                            ));
	                                            
	    } catch (Zend_Gdata_App_Exception $e) {
	        
	    }
	                                      
	    $select = $this->registry->db->select();
	    
	    $select->from(array('y' => 'youtube'));
		$select->where('yt_author = ?',$params['author']);
		
		if($params['type'] != 'all') :
		    $select->where('yt_sync = ?','Y');
        endif;

		if($params['sort'] == 'published') :
		    $select->order(array('y.yt_date DESC'));
		elseif ($params['sort'] == 'title') :
		    $select->order(array('y.yt_title ASC'));
		else :
            $select->order(array('y.yt_date DESC'));
        endif;
        
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($params['page']);
		$paginator->setItemCountPerPage($params['items']);
		$paginator->setPageRange($params['range']);
	    
    	return $paginator;
	}
	
    /**
     *  Fetches a video from the feed, syncs it and returns video array 
     *  @param string $key Video key
     *  @param string $author Restrict to this YouTube user
     */
	public function fetchVideoFeed($key,$author,$id = null)
	{
	    if($key != NULL) :
	    
	        try {    
	           $feed = $this->feedVideo($key);
	        } catch (Zend_Gdata_App_Exception $e) {
	        
	        }
	        
	        if($feed && $author == $feed->author[0]->name->text) :
	        
	            try {
	                $this->syncFeed($feed);
	            } catch (Zend_Gdata_App_Exception $e) {
	        
	            }
	    
	            $select = $this->registry->db->select()->from(array('y' => 'youtube'))
    	                                               ->where('y.yt_key = ?', $key)
    	                                               ->limit(1,0);

		        $video = $this->registry->db->fetchall($select);
		    
		        return $video['0'];
		        
		    else :
		    
		        $data = array(
        	        'yt_sync' => 'N'
                );
                $this->registry->db->update('youtube', $data, 'yt_key = "'.$key.'"');
		        
		    endif;
		    
		elseif ($id != NULL) :
		
		    $select = $this->registry->db->select()->from(array('y' => 'youtube'))
    	                                               ->where('y.yt_id = ?', $id)
    	                                               ->limit(1,0);

		    $entry = $this->registry->db->fetchall($select);
		    
		    $entry =  $entry['0'];
		    
		    $feed = $this->feedVideo($entry['yt_key']);
	        
	        if($feed && $author == $feed->author[0]->name->text) :
	        
	            return $entry;
		        
		    else :
		    
		        $data = array(
        	        'yt_sync' => 'N'
                );
                $this->registry->db->update('youtube', $data, 'yt_id = "'.$entry['yt_id'].'"');
                
                $select = $this->registry->db->select()->from(array('y' => 'youtube'))
    	                                               ->where('y.yt_id = ?', $id)
    	                                               ->limit(1,0);

		        $entry = $this->registry->db->fetchall($select);
		    
		        return  $entry['0'];
		        
		    endif;
		    
		else :
		
		    return false;
		    
		endif;
	}
	
	/**
     *  Fetches the latest video from the feed, syncs it and returns video array 
     *  @param string $key Video key
     *  @param string $author Restrict to this YouTube user
     */
	public function fetchLatestVideoFeed($params)
	{
	    $select = $this->registry->db->select()->from(array('y' => 'youtube'))
				   		                       ->order(array('y.yt_date DESC'));
				   		                       
		$videoArray = $this->registry->db->fetchall($select);
		
		return $videoArray['0'];
	}
	
	public function fetchVideoComments($key,$items,$offset,$page)
	{
	    $cache_id = 'youtube_comments_' . md5($key) .'_page_' . $page;

        if(!($comments = $this->_cache->load($cache_id)))
        {
	        $yt = new Zend_Gdata_YouTube();
            $yt->setMajorProtocolVersion(2);
        
            $comments = $yt->getVideoCommentFeed(null,'http://gdata.youtube.com/feeds/api/videos/'.$key.'/comments?orderby=published&max-results='.$items.'&start-index='.$offset);
        
            $this->_cache->save($comments, $cache_id, array('youtube'), '10800');
            
        }
        
        return $comments;
	    
	    
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