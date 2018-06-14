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
class Zend_View_Helper_MReleaseSelect
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function MReleaseSelect ()
    {
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select();
    	$select->from(array('r' => 'releases'));
    	$select->where('r.release_status = ?','published');
    	$select->order('r.release_title ASC');

		// Set the data array
		$releaseArray = $registry->db->fetchall($select);
		
		foreach($releaseArray as $release) :
			echo '<option value="' . $release['release_id'] . '">' . $release['release_title'] . '</option>';
		endforeach;
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
