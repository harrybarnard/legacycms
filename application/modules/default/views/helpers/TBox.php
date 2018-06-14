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
class Zend_View_Helper_TBox
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function TBox($type = 'ALL',$action = 'articles',$num = 'ALL')
    {
        $fc = Zend_Controller_Front::getInstance();
        $baseUrl = $fc->getBaseUrl();
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
        if ($num != 'ALL') {
    	    if ($type != 'ALL') {
    	        $select = $registry->db->select()
    					           ->from(array('t' => 'tags'))
				   		           ->where('tag_type = ?',$type)
				   		           ->limit($num, 0)
				   		           ->order(new Zend_Db_Expr('RAND()'));
    	    } else {
    	        $select = $registry->db->select()
    					           ->from(array('t' => 'tags'))
				   		           ->limit($num, 0)
				   		           ->order(new Zend_Db_Expr('RAND()'));
    	    }
    	} else {
    	    if ($type != 'ALL') {
    	        $select = $registry->db->select()
    					           ->from(array('t' => 'tags'))
				   		           ->where('tag_type = ?',$type);
		    } else {
		        $select = $registry->db->select()
    					           ->from(array('t' => 'tags'));
		    }
    	}	
    			   
    	$tagArray = $registry->db->fetchall($select);
    	
        $cloud = new CMS_Wordcloud_Wordcloud();
        if ($tagArray) {
            foreach ($tagArray as $tag) {
                $cloud->addWord($tag['tag_tag']);
            }
        }
        
        $myCloud = $cloud->showCloud('array');
        
        if($this->view->placeholder('showTags') != 'false') :
        
            if (is_array($myCloud) & count($tagArray) > 0) :
				echo '<div class="rBox">
				    <h3>Tags</h3>
				    <div class="wordCloud">';
                foreach ($myCloud as $value) :
                    echo ' <a href="'.$this->view->baseUrl().'/'.$action.'/tag/'.urlencode($value['word']).'/" class="word size'.($value['range'] - 1).'">'.$value['word'].'</a> ';
                endforeach;
                echo '</div>
                 	<div class="rMore">
						<a href="'.$this->view->baseUrl().'/tags/">More Tags &raquo;</a>
					</div>
				 	</div>
				    <p class="Spacer"></p>';
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
