<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Tags
{
    /**
     * Constructor
     * @return void
     */
    public function __construct ()
    {
        $this->registry = Zend_Registry::getInstance();
    }
    
    public function deleteTag($id)
    {
        if ($id != NULL && is_numeric($id)) :
        
		    $this->registry->db->delete('tags', 'tag_id = '.$id);
		    
		else :
		    
		    throw new Exception('Invalid tag id');
		    
        endif;
    }
    
    public function deleteSlaveTag($type,$slave)
    {
        if ($type != NULL & $slave != NULL && is_numeric($slave)) :
        
		    $this->registry->db->delete('tags', array('tag_type = "'.$type.'"','tag_slave = '.$slave));
		    
		else :
		    
		    throw new Exception('Invalid parameters');
		    
        endif;
    }
}
?>