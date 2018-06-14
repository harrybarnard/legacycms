<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class CMS_Acl_Factory {
    /**
    * Creates an ACL for a specific resource
    * @param string $resource
    * @return Zend_Acl
    */
    public function createResourceAcl($resource) {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    	                       ->from(array('p' => 'users_privileges'))
    	                       ->where('p.prv_resource = ?', $resource);

		// Set the data array
		$privileges = $registry->db->fetchall($select);
 
        $acl = new Zend_Acl();
        $acl->add(new Zend_Acl_Resource($resource));
        
        foreach($privileges as $privilege) {
            $acl->addRole(new Zend_Acl_Role($privilege['prv_role']));
            $acl->allow($privilege['prv_role'], $resource);
        }
            
        return $acl;
    }
    
	/**
    * Creates an ACL for all global resources
    * @return Zend_Acl
    */
    public function createGlobalAcl() {
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the resources query
    	$select = $registry->db->select()
    	                       ->from(array('r' => 'users_resources'));
    	                       
    	// Set the data array
		$resources = $registry->db->fetchall($select);
		
		$acl = new Zend_Acl();
		
		// Build the roles query
    	$select = $registry->db->select()
    	                       ->from(array('r' => 'users_roles'));
    	                       
    	// Set the data array
		$roles = $registry->db->fetchall($select);
		
		foreach($roles as $role) {
		    $acl->addRole(new Zend_Acl_Role($role['role_id']));
		}
		
        foreach($resources as $resource) {
            $acl->add(new Zend_Acl_Resource($resource['res_resource']));
        }
        
        // Build the query
    	$select = $registry->db->select()
    	                       ->from(array('p' => 'users_privileges'));

		// Set the data array
		$privileges = $registry->db->fetchall($select);

        foreach($privileges as $privilege) {
            $acl->allow($privilege['prv_role'], $privilege['prv_resource']);
        }
    	                       
        return $acl;
    }
    
}
?>