<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * TagCloud helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_ATagCloud
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function ATagCloud($action = 'articles',$num = 'ALL')
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseUrl = $fc->getBaseUrl();
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	if ($num != 'ALL') {
    	    $select = $registry->db->select()
    		    	           ->from(array('t' => 'tags'))
    				           ->join(array('a' => 'articles'),'t.tag_slave = a.article_id',array('a.article_status'))
					           ->where('t.tag_type = ?','A')
					           ->where('a.article_published <= NOW()')
					           ->where('a.article_status = ?','published')
					           ->limit($num, 0)
					           ->order(new Zend_Db_Expr('RAND()'));
    	} else {
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'tags'))
    					       ->join(array('a' => 'articles'),'t.tag_slave = a.article_id',array('a.article_status'))
				   		       ->where('t.tag_type = ?','A')
				   		       ->where('a.article_published <= NOW()')
				   		       ->where('a.article_status = ?','published');
    	}
    					   
    	$tagArray = $registry->db->fetchall($select);
    	
        $cloud = new CMS_Wordcloud_Wordcloud();
        if ($tagArray) {
            foreach ($tagArray as $tag) {
                $cloud->addWord($tag['tag_tag']);
            }
        }
        
        $myCloud = $cloud->showCloud('array');
        
        if (count($myCloud)) {
        
            if (is_array($myCloud)) {
                echo '<div class="wordCloud">';
                foreach ($myCloud as $value) {
                    echo ' <a href="/'.$this->view->baseUrl().$action.'/tag/'.urlencode($value['word']).'/" class="word size'.($value['range'] - 1).'">'.$value['word'].'</a> ';
                }
                 echo '</div>';
            }
            
        } else {
            echo '<div class="cInfo">No tags!</div>';
        }
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
