<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ArtRecentArtList helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ARecentList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function ARecentList ($num,$links = false)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('a' => 'articles'),array('a.*','article_day' => 'DAY(a.article_published)','article_month' => 'MONTH(a.article_published)','article_year' => 'YEAR(a.article_published)'))
				   		   ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
				   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
				   		   ->where('article_published <= NOW()')
				   		   ->where('article_status = ?','published')
    					   ->order('a.article_date DESC')
    					   ->limit($num, 0);
    					   
    	$articleArray = $registry->db->fetchall($select);
    	
    	if(count($articleArray) > 0) :
    	
    	    echo '<div class="rBox">
				<h3>Recent Articles</h3>';
    	
    	    foreach($articleArray as $article) :
                echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/articles/article/'.$article['article_id'].'/'.urlencode($article['article_title']).'\/\');">
				  	<a href="'.$this->view->baseUrl().'/articles/article/'.$article['article_id'].'/'.urlencode($article['article_title']).'/">'.$article['article_title'].'</a>
				  	<small>'.$this->view->ADate($article['article_published']).'</small>
				  </div>';
            endforeach;
            if($links == true) :
                echo '<div class="rMore">
					<a href="'.$this->view->baseUrl().'/articles/">More Articles &raquo;</a>
				</div>';
            endif;
            echo '</div>
        		<p class="Spacer"></p>';
        
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
