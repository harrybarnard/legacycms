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
class Zend_View_Helper_RenderRotator
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     * Build the options for an articles category select 
     */
    public function RenderRotator ($rotName)
    {
	    // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    	                       ->from(array('r' => 'rotators'))
    	                       ->where('r.rot_name = ?', $rotName)
    	                       ->limit(1,0);

		// Set the data array
		$rotatorArray = $registry->db->fetchall($select);

        $rotator = $rotatorArray['0'];
        
        // If the rotator exists
        if(count($rotator)) :
        
            // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('s' => 'rotators_slides'))
    	                           ->where('s.rots_rotator = ?', $rotator['rot_id'])
    	                           ->order('s.rots_order DESC');

		    // Set the data array
		    $slidesArray = $registry->db->fetchall($select);
		    
		    // If the rotator has slides
		    if (count($slidesArray)) :
		
		        $i = 1;
		
		        if($rotator['rot_paging'] == 'Y') :
		            $tabs = 'true';
		        else :
		            $tabs = 'false';
		        endif;
		        
		        echo '<div class="rotContainer js-on" style="width: '.$rotator['rot_width'].'px; height: '.$rotator['rot_height'].'px;">';
		
		        echo '<div class="js-on" dojoType="dojox.layout.RotatorContainer" id="rot'.$rotator['rot_name'].'" showTabs="'.$tabs.'" suspendOnHover="true" autoStart="true" transitionDelay="'.$rotator['rot_delay'].'" transition="fade" style="width: '.$rotator['rot_width'].'px; height: '.$rotator['rot_height'].'px;">';
        
		        foreach ($slidesArray as $slide) :
		
		            echo '<div id="'.$rotator['rot_name'].$i.'" dojoType="dijit.layout.ContentPane" title="'.$i.'" class="rotatorPane" style="background: url(\''.$this->view->baseUrl().'/assets/thumb/'.$slide['rots_asset'].'/'.($rotator['rot_width'] + 2).'/'.($rotator['rot_height'] + 2).'/\');" onclick="goTo(\''.$slide['rots_link'].'\')">';
		            echo '<div class="rContent">';
			        echo '<h2>'.$slide['rots_title'].'</h2>';
			        if($slide['rots_description'] != NULL) :
			            echo '<p>'.$slide['rots_description'].'</p>';
			        endif;
		            echo '</div>';
	                echo '</div>';
	        
	                $i++;
		
		        endforeach;
		
		        echo '</div>';
		        echo '</div>';
                echo '<p class="Spacer js-on"></p>';
                
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
