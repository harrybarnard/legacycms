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
class Zend_View_Helper_AssetFile
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function AssetFile ($key = NULL)
    {
	    if($key != NULL) :
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_key = ?', $key)
    	                       ->limit(1,0);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);

            if(count($assetArray)) :
            
                $assArray = $assetArray['0'];
    	
		        return $assArray['asset_name'].'.'.$assArray['asset_extension'];
		        
		    else :
		    
		        return 'Asset Missing!';
		        
		    endif;
		    
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
