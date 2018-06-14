<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Attachments
{
    /**
     * Constructor
     * @return void
     */
    public function __construct ()
    {
        $this->registry = Zend_Registry::getInstance();
    }
    
    public function deleteAttachment($id)
    {
        if ($id != NULL && is_numeric($id)) :
        
		    $this->registry->db->delete('attachments', 'attach_id = '.$id);
		    
		else :
		    
		    throw new Exception('Invalid attachment id');
		    
        endif;
    }
    
    public function deleteSlaveAttachment($type,$slave)
    {
        if ($type != NULL & $slave != NULL && is_numeric($slave)) :
        
		    $this->registry->db->delete('attachments', array('attach_type = "'.$type.'"','attach_slave = '.$slave));
		    
		else :
		    
		    throw new Exception('Invalid parameters');
		    
        endif;
    }
    
    public function fetchAttachments($params)
    {
        $select = $this->registry->db->select();
    	$select->from(array('a' => 'attachments'));
    	$select->join(array('f' => 'assets'),'f.asset_key = a.attach_asset',array('f.*'));
    	if(isset($params['type'])) :
    	    $select->where('a.attach_type = ?',$params['type']);
    	endif;
    	if(isset($params['slave'])) :
    	    $select->where('a.attach_slave = ?',$params['slave']);
    	endif;
    	$select->order('f.asset_name ASC');
		
    	return $attachments = $this->registry->db->fetchall($select);
    }
}
?>