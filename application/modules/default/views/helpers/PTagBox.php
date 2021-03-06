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
class Zend_View_Helper_PTagBox
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function PTagBox($num = 'ALL')
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
        if ($num != 'ALL') {
    	    $select = $registry->db->select()
    		    	           ->from(array('t' => 'tags'))
    				           ->join(array('p' => 'pages'),'t.tag_slave = p.page_id',array('p.page_status'))
					           ->where('t.tag_type = ?','P')
					           ->where('p.page_status = ?','published')
					           ->limit($num, 0)
					           ->order(new Zend_Db_Expr('RAND()'));
    	} else {
    	    $select = $registry->db->select()
    					       ->from(array('t' => 'tags'))
    					       ->join(array('p' => 'pages'),'t.tag_slave = p.page_id',array('p.page_status'))
				   		       ->where('t.tag_type = ?','P')
				   		       ->where('p.page_status = ?','published');
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
				    <h3>Page Tags</h3>
				    <div class="wordCloud">';
                foreach ($myCloud as $value) :
                    echo ' <a href="'.$this->view->baseUrl().'/pages/tag/'.urlencode($value['word']).'/" class="word size'.($value['range'] - 1).'">'.$value['word'].'</a> ';
                endforeach;
                echo '</div>
                 	<div class="rMore">
						<a href="'.$this->view->baseUrl().'/pages/tags/">More Tags &raquo;</a>
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
