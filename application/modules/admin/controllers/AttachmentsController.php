<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_AttachmentsController extends CMS_Controller_Action_Admin
{
    
    public function setLayout()
	{
		// Change layout to Location
    	$this->_helper->layout->setLayout('admin');	
	}

	/**
     * Index Action
     */
    public function indexAction ()
    {   
        $this->setLayout();
    }
    
	/**
     * Show Action - Render a list of tags
     */
    public function showAction () {  
         
        $this->_helper->layout->disableLayout(); // Disable Layouts
        
        $slave = $this->_getParam('slave');
        $type = $this->_getParam('type');
        $this->view->status = $this->_getParam('status');        
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    	                       ->from(array('a' => 'attachments'))
    	                       ->join(array('f' => 'assets'),'f.asset_key = a.attach_asset',array('f.*'))
    	                       ->where('a.attach_type = ?', $type)
    	                       ->where('a.attach_slave = ?', $slave);

		// Set the data array
		$this->view->attachmentArray = $registry->db->fetchall($select);
        
    }
    
	/**
     * Delete Action - remove a tag from the database
     */
    public function deleteAction () {   
        
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        $id = $this->_getParam('id');
        
        if (isset($id)) :
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
		    // Delete the row
		    $registry->db->delete('attachments', 'attach_id = '.$id);
		    
		    echo '<p class="Spacer"></p>
			<div class="cUpd">Attachment Removed</div>
			<p class="Spacer"></p>
			<div align="center">
				<button dojoType="dijit.form.Button" type="button" id="acancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
			</div>';
		    
        endif;
        
    }
    
	/**
     * New Action - add a tag to the database
     */
    public function newAction () {   
        
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        if($this->_request->isPost()) :
        
            $type = $this->_getParam('type');
            $slave = $this->_getParam('slave');
            $asset = $this->_getParam('asset');
            
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	    
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'attachments'))
    	                       ->where('a.attach_type = ?', $type)
    	                       ->where('a.attach_slave = ?', $slave)
    	                       ->where('a.attach_asset = ?', $asset);

		    // Set the data array
		    $attachmentArray = $registry->db->fetchall($select);
		    
		    if (count($attachmentArray)) :
		        $unique = 'false';
		    else :
		        $unique = 'true';
		    endif;
            
            if ($unique == 'true') :
            
                $options = array(
            		'missingMessage' => "Field '%field%' is required"
                );

                $filters = array(
            		'tag' => array('StringTrim', 'StripTags')
                );
                
                $validators = array(
                	'asset' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Attachment is required"))
                    ),
                	'type' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Type is required"))
                    ),
                	'slave' => array(
                    	'presence' => 'required',
                    	'NotEmpty',
                 		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Slave is required"))
                    ),
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                // setup database
    	        $registry = Zend_Registry::getInstance();

                if ($input->isValid()) :
                
                    // Create our data array
                    $data = array(
    					'attach_type'     => $input->type,
    					'attach_asset'    => $input->asset,
                		'attach_slave'	  => $input->slave,
                    );

                    // Insert data into database
                    $registry->db->insert('attachments', $data);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Attachment Added</div>
					<p class="Spacer"></p>
					<div align="center">
						<button dojoType="dijit.form.Button" type="button" id="acancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                
                else :
                
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="attachsavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                    endif;
                    
                endif;
                
            else :
            
                echo '<p class="Spacer"></p>
				<div class="cErr">Duplicate Attachment</div>
				<p class="Spacer"></p>
				<div align="center">
					<button dojoType="dijit.form.Button" type="button" id="acancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
            endif;
            
        else :
            echo 'No data received';
        endif;
    }
    
	/**
     * Attachment Tab Action - count tags and update tab title
     */
    public function tabAction () {   
        
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$type = $this->_request->getParam('type');
    	$slave = $this->_request->getParam('slave');
    	
    	$select = $registry->db->select()
    					   ->from(array('a' => 'attachments'))
				   		   ->where('attach_slave = ?',$slave)
				   		   ->where('attach_type = ?',$type);
    					   
    	$attachmentArray = $registry->db->fetchall($select);
    	
    	echo 'Attachments ('.count($attachmentArray).')';
    }
}