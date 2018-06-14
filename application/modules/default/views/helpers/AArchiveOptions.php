<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ArtCategoryList helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_AArchiveOptions
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    
    private function archiveCount($month,$year)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('a' => 'articles'))
    					   ->where('article_published <= NOW()')
				       	   ->where('article_status = ?','published')
				       	   ->where('month(article_published) = ?',$month)
				   		   ->where('year(article_published) = ?',$year)
    					   ->order('a.article_date DESC');
    					   
        $articlesArray = $registry->db->fetchall($select);
        
        return count($articlesArray);
    }
    /**
     *  
     */
    public function AArchiveOptions()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load assets configuration
        $articles = new Zend_Config_Ini('../application/configs/articles.ini', array('config'));
        $registry->set('articles', $articles);
        
        $start = strtotime($registry->articles->archivestart);
        $now = strtotime("Now");
        
        echo '<div class="rBox js-on">
			  <h3>Browse The Archives</h3>
			  <select dojoType="dijit.form.FilteringSelect" name="archive" id="archive" autocomplete="false" style="width: 265px;" class="cFormSelect" onchange="goTo(this.value);">
        	      <option value="#" selected>Pick a Month</option>';

	    while ($now > $start) {
	        
	        $count = $this->archiveCount(date("m", $now),date("Y", $now));

            echo '<option value="'.$this->view->baseUrl().'/articles/archive/year/'.date("Y", $now).'/month/'.date("m", $now).'/">'.date("F Y", $now).' ('.$count.')</option>';

            $now = strtotime("-1 month", $now);

	    }
	    
	    echo '</select>
			  </div>
			  <p class="Spacer js-on"></p>';

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
