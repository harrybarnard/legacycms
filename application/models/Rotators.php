<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Rotators
{
	/**
     * Constructor
     * @return void
     */
    public function __construct()
    {
        $this->registry = Zend_Registry::getInstance();
    }
	
	/**
     *  Fetches rotators and returns a paginator array 
     *  @param array $params Array of params to filter by
     *  @return array $paginator Paginated array of rotators
     */
	public function fetchRotators($params = NULL)
	{
    	$select = $this->registry->db->select();
    	$select->from(array('r' => 'rotators'));
    	$select->order('r.rot_name ASC');
    	
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
     *  Fetches slides and returns a paginator array 
     *  @param array $params Array of params to filter by
     *  @return array $paginator Paginated array of slides
     */
	public function fetchSlides($params = NULL)
	{
    	$select = $this->registry->db->select();
    	$select->from(array('s' => 'rotators_slides'));
    	
    	if(isset($params['rotator'])) :
    	    $select->where('s.rots_rotator = ?', $params['rotator']);
    	endif;
    	
    	$select->order('s.rots_title ASC');
    	
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
     *  Fetch rotator and return as array
     *  @param integer $id Rotator id
     *  @return array $rotator Rotator
     */
	public function fetchRotator($id)
	{
    	if ($id != NULL && is_numeric($id)) :
	        $select = $this->registry->db->select()
    	                                 ->from(array('r' => 'rotators'))
    	                                 ->where('r.rot_id = ?', $id)
    	                                 ->limit(1,0);

		    $rotator = $this->registry->db->fetchall($select);
            return $rotator['0'];
        else :
            throw new Exception('Invalid rotator id');
        endif;
	}
	
	/**
     *  Fetch slide and return as array
     *  @param integer $id Slide id
     *  @return array $slide Slide
     */
	public function fetchSlide($id)
	{
    	if ($id != NULL && is_numeric($id)) :
	        $select = $this->registry->db->select()
    	                                 ->from(array('s' => 'rotators_slides'))
    	                                 ->where('s.rots_id = ?', $id)
    	                                 ->limit(1,0);

		    $slide = $this->registry->db->fetchall($select);
            return $slide['0'];
        else :
            throw new Exception('Invalid slide id');
        endif;
	}
	
	/**
     *  Create new slide
     *  @param array $params Page params
     */
	public function newSlide($params = NULL)
	{
        if ($params['title'] != NULL) :

            $data = array(
                'rots_title'        => $params['title'],
                'rots_rotator'      => $params['rotator'],
                'rots_description'  => $params['description'],
                'rots_link'         => $params['link'],
                'rots_order'        => $params['priority'],
                'rots_asset'        => $params['asset'],
            );

            $this->registry->db->insert('rotators_slides', $data);
        else: 
            throw new Exception('Invalid parameters');
        endif;
	}
	
	/**
     *  Delete slide
     *  @param integer $id Slide id
     */
	public function deleteSlide($id)
	{
	    if (isset($id) && is_numeric($id)) :
	    
            $this->registry->db->delete('rotators_slides', 'rots_id = '.$id);
        else :
            throw new Exception('Invalid slide id');
        endif;
	}
	
	/**
     *  Updates slide 
     *  @param array $params Slide params
     */
	public function updateSlide($params = NULL)
	{
         $data = array(
         	'rots_title'        => $params['title'],
            'rots_description'  => $params['description'],
            'rots_link'         => $params['link'],
            'rots_order'        => $params['priority'],
            'rots_asset'        => $params['asset'],
         );

         $this->registry->db->update('rotators_slides', $data, 'rots_id = '.$params['id']);
	}
	
}
?>