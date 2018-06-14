<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * Privileges Form helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_UPrvForm
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function UPrvForm($module,$role)
    {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$groupselect = $registry->db->select()
    	                       ->from(array('e' => 'users_resources'))
    	                       ->where('e.res_module = ?', $module)
    	                       ->group('e.res_group');
    	                       
    	// Set the data array
		$groups = $registry->db->fetchall($groupselect);
    	                       
    	foreach($groups as $group) {
    	    
    	    echo '<fieldset class="cFieldset">
		    	<legend class="cFLegend">'.ucwords($group['res_group']).'</legend>';
    	    
    	    // Build the query
    	    $select = $registry->db->select()
    	                           ->from(array('e' => 'users_resources'))
    	                           ->where('e.res_module = ?', $group['res_module'])
    	                           ->where('e.res_group = ?', $group['res_group'])
    	                           ->order('e.res_resource ASC');

		    // Set the data array
		    $resources = $registry->db->fetchall($select);
    					   
            foreach($resources as $resource) {
                $factory = new CMS_Acl_Factory();
                $acl = $factory->createResourceAcl($resource['res_resource']);
                echo '<div class="cFormItm">
            		<label for="'.$resource['res_id'].'"><input dojotype="dijit.form.CheckBox" name="resources[]" id="'.$resource['res_id'].'" value="'.$resource['res_resource'].'" type="checkbox"';
                if($acl->hasRole($role) && $acl->isAllowed($role, $resource['res_resource'])) {
                    echo ' checked="checked"';
                    if($role == 3) {
                        echo ' readonly="readonly"';
                    }
                }
                echo '/> '.$resource['res_description'].'</label>
            		</div>';
            }
            
            echo '</fieldset>';
            
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
