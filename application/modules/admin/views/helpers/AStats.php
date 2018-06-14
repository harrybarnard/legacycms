<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * AStats helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_AStats
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * 
     */
    public function aStats ()
    {
        $registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
    					       ->from(array('a' => 'articles'));
    					     
    	// Set the data array
		$articlesArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('a' => 'articles'))
    					       ->where('a.article_status = ?', 'published');
    					     
    	// Set the data array
		$publishedArray = $registry->db->fetchall($select);
		
		$select = $registry->db->select()
    					       ->from(array('a' => 'articles'))
    					       ->where('a.article_status = ?', 'draft');
    					     
    	// Set the data array
		$draftArray = $registry->db->fetchall($select);
		
		return '<span>Total Articles: '.count($articlesArray).'</span> <span>Published: '.count($publishedArray).'</span> <span>Draft: '.count($draftArray).'</span>';
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

