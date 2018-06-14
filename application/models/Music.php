<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Music
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
        
        $articles = new Zend_Config_Ini('../application/configs/articles.ini', array('config'));
        $this->registry->set('articles', $articles);
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
            $filter['sort'] = 'date';
        endif;
        
        if (isset($params['order'])) :
            if ($params['order'] == 'desc') :
                $filter['order'] = 'desc';
                $filter['orderopt'] = 'asc';
            else :
                $filter['order'] = 'asc';
                $filter['orderopt'] = 'desc';
            endif;
        else :
            $filter['order'] = 'desc';
            $filter['orderopt'] = 'asc';
        endif;
	    
	    return $filter;
	}
	
	/**
     *  Fetches tracks based on filter params and returns a paginator array 
     *  @param array $params Array of params to filter by
     *  @return array $paginator Paginated array of tracks
     */
	public function fetchTracks($params)
	{
        if(isset($params['order'])) :
    	    $order = strtoupper($params['order']);
    	else :
    	    $order = 'DESC';
        endif;
        
    	$select = $this->registry->db->select();
    	$select->from(array('m' => 'music'));
    	$select->join(array('r' => 'releases'),'r.release_id = m.music_release',array('r.release_title'));
    	
    	if (isset($params['sort'])) :
    	
    	    if ($params['sort'] == 'date') :
    	        $select->order('m.music_date '.$order);
    	    elseif ($params['sort'] == 'track') :
    	        $select->order('m.music_title '.$order);
    	    elseif ($params['sort'] == 'release') :
    	        $select->order('m.music_release '.$order);
    	    endif;
    	    
    	else :
    	    $select->order('m.music_date '.$order);
    	endif;
    	
    	if(isset($params['page']) && is_numeric($params['page'])) :
    		$pagenum = $params['page'];
    	else :
    		$pagenum = 1;
    	endif;
    	
    	if(isset($params['items']) && is_numeric($params['items'])) :
    		$items = $params['items'];
    	else :
    		$items = 15;
    	endif;
    	
    	if(isset($params['range']) && is_numeric($params['range'])) :
    		$range = $params['range'];
    	else :
    		$range = 5;
    	endif;
    	
    	$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($pagenum);
		$paginator->setItemCountPerPage($items);
		$paginator->setPageRange($range);
	    
    	return $paginator;
	}
	
	/**
     *  Fetches tracks and returns an array 
     *  @param array $params Array of params to filter by
     *  @return array $tracks Array of tracks
     */
	public function fetchPlaylist($params = NULL)
	{
        if($params['playlist'] == 'all') :
	    
	        if(isset($params['order'])) :
    	        $order = strtoupper($params['order']);
    	    else :
    	        $order = 'DESC';
            endif;
	    
	        $select = $this->registry->db->select();
    	    $select->from(array('m' => 'music'));
    	    $select->join(array('r' => 'releases'),'r.release_id = m.music_release',array('r.release_id','r.release_title'));
    	
    	    if (isset($params['sort'])) :
    	
    	        if ($params['sort'] == 'date') :
    	            $select->order('m.music_date '.$order);
    	        elseif ($params['sort'] == 'track') :
    	            $select->order('m.music_title '.$order);
    	        elseif ($params['sort'] == 'random') :
    	            $select->order(new Zend_Db_Expr('RAND()'));
    	        elseif ($params['sort'] == 'release') :
    	            $select->order('m.music_release '.$order);
    	        endif;
					           
    	    else :
    	        $select->order('m.music_date '.$order);
    	    endif;
    	
    	    if (isset($params['limit'])) :
    	        $select->limit($params['limit'], 0);
    	    endif;
    	    
    	    return $tracks = $this->registry->db->fetchall($select);
		
    	elseif($params['playlist'] == 'random') :
    	
    	    $select = $this->registry->db->select();
    	    $select->from(array('m' => 'music'));
    	    $select->join(array('r' => 'releases'),'r.release_id = m.music_release',array('r.release_title'));
    	    $select->limit(6, 0);
			$select->order(new Zend_Db_Expr('RAND()'));
			
			return $tracks = $this->registry->db->fetchall($select);
    	
    	endif;
	}
	
    /**
     *  Fetch track and return as array
     *  @return array $track Track
     */
	public function fetchTrack($id)
	{
    	if ($id != NULL && is_numeric($id)) :
	        $select = $this->registry->db->select()
    	                                 ->from(array('m' => 'music'))
    	                                 ->where('m.music_id = ?', $id)
    	                                 ->limit(1,0);

		    $track = $this->registry->db->fetchall($select);
            return $track['0'];
        else :
            throw new Exception('Invalid track id');
        endif;
	}
	
	/**
     *  Create new track
     *  @param array $params Page params
     */
	public function newTrack($params = NULL)
	{
        if ($params['title'] != NULL) :

            $data = array(
                'music_title'       => $params['title'],
                'music_description' => $params['description'],
                'music_release'     => $params['release'],
                'music_visual'		=> $params['visual'],
                'music_asset'		=> $params['asset'],
                'music_play'		=> $params['play'],
                'music_download'	=> $params['download'],
                'music_date'        => new Zend_Db_Expr('NOW()')
            );

            $this->registry->db->insert('music', $data);
        else: 
            throw new Exception('Invalid parameters');
        endif;
	}
	
	/**
     *  Delete track
     *  @param integer $id Track id
     */
	public function deleteTrack($id)
	{
	    if (isset($id) && is_numeric($id)) :
	    
            $this->registry->db->delete('music', 'music_id = '.$id);
        else :
            throw new Exception('Invalid track id');
        endif;
	}
	
	/**
     *  Updates track 
     *  @param array $params Track params
     */
	public function updateTrack($params)
	{
         $data = array(
            'music_title'       => $params['title'],
            'music_description' => $params['description'],
            'music_release'     => $params['release'],
            'music_visual'		=> $params['visual'],
            'music_asset'		=> $params['asset'],
            'music_play'		=> $params['play'],
            'music_download'	=> $params['download']
         );

         $this->registry->db->update('music', $data, 'music_id = '.$params['id']);
	}
	
}
?>