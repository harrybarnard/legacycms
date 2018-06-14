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
class Zend_View_Helper_EditCatSelect
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function editCatSelect ($category)
    {
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->query('SELECT * FROM articles_categories');

		// Set the data array
		$catArray = $select->fetchall();
		
		foreach($catArray as $cat) :
		    if ($cat['acat_id'] == $category) :
			    echo '<option value="' . $cat['acat_id'] . '" selected="selected">' . $cat['acat_title'] . '</option>';
			else :
			    echo '<option value="' . $cat['acat_id'] . '">' . $cat['acat_title'] . '</option>';
			endif;
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
