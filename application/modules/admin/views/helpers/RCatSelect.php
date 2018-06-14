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
class Zend_View_Helper_RCatSelect
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function rcatSelect ()
    {
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->query('SELECT * FROM resources_categories');

		// Set the data array
		$catArray = $select->fetchall();
		
		foreach($catArray as $cat) :
			echo '<option value="' . $cat['rcat_id'] . '">' . $cat['rcat_title'] . '</option>';
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
