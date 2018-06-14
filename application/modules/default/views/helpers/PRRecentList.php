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
class Zend_View_Helper_PRRecentList
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function PRRecentList ($links = false)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					       ->from(array('p' => 'products'))
				       		   ->where('p.product_status = ?','published')
    					       ->order('p.product_published DESC')
    					       ->limit(3, 0);
    					   
    	$productArray = $registry->db->fetchall($select);
    	
    	if(count($productArray) > 0) :
    	
    	    echo '<div class="rBox">
				<h3>New Stuff to Buy</h3>';
    	    foreach($productArray as $product) :
            echo '<div class="lBox lBoxOff" onmouseover="this.className=\'lBox lBoxOn\';" onmouseout="this.className=\'lBox lBoxOff\';" onclick="goTo(\''.$this->view->baseUrl().'/products/product/'.$product['product_id'].'/'.urlencode($product['product_title']).'\/\');">
				  	<img src="'.$this->view->baseUrl().'/assets/thumb/'.$product['product_asset'].'/60/60/" border="0" width="60" height="60"/>
            		<a href="'.$this->view->baseUrl().'/products/product/'.$product['product_id'].'/'.urlencode($product['product_title']).'/">'.$product['product_title'].'</a>
				  	<p>&pound;'.number_format($product['product_price'], 2, '.', ',').'</p>
				  	<p class="ClearAll"></p>
				  </div>';
            endforeach;
            if($links == true) :
                echo '<div class="rMore">
					<a href="'.$this->view->baseUrl().'/products/">More Stuff to Buy &raquo;</a>
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
