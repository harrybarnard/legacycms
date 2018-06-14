<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
class Admin_TagsController extends CMS_Controller_Action_Admin
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
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	// Build the query
    	$select = $registry->db->select()
    	                       ->from(array('t' => 'tags'))
    	                       ->where('t.tag_type = ?', $type)
    	                       ->where('t.tag_slave = ?', $slave);

		// Set the data array
		$this->view->tagArray = $registry->db->fetchall($select);
        
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
		    $registry->db->delete('tags', 'tag_id = '.$id);
		    
		    echo '<p class="Spacer"></p>
			<div class="cUpd">Tag Removed</div>
			<p class="Spacer"></p>
			<div align="center">
				<button dojoType="dijit.form.Button" type="button" id="tcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
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
            $tag = $this->_getParam('tag');
            
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	    
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('t' => 'tags'))
    	                       ->where('t.tag_type = ?', $type)
    	                       ->where('t.tag_slave = ?', $slave)
    	                       ->where('t.tag_tag = ?', $tag);

		    // Set the data array
		    $tagArray = $registry->db->fetchall($select);
		    
		    if (count($tagArray)) :
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
                	'tag' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Tag is required"))
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
    					'tag_type'     	=> $input->type,
    					'tag_tag'       => $input->tag,
                		'tag_slave'	    => $input->slave,
                    );

                    // Insert data into database
                    $registry->db->insert('tags', $data);
                    
                    echo '<p class="Spacer"></p>
					<div class="cUpd">Tag Added</div>
					<p class="Spacer"></p>
					<div align="center">
						<button dojoType="dijit.form.Button" type="button" id="tcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
					</div>';
                
                else :
                
                    // Set Error Messages
                    $this->messages = $input->getMessages();
                
                    if (isset($this->messages)) :
                        $this->_helper->messages->render($this->messages);
                        echo '<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="pagesavecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
						</div>';
                    endif;
                    
                endif;
                
            else :
            
                echo '<p class="Spacer"></p>
				<div class="cErr">Duplicate Tag</div>
				<p class="Spacer"></p>
				<div align="center">
					<button dojoType="dijit.form.Button" type="button" id="tcancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Close</button>
				</div>';
            endif;
            
        else :
            echo 'No data received';
        endif;
    }
    
    /**
     * Autocomplete Action - populate the tag autocomplete
     */
    public function autocompleteAction() {
        
        $this->_helper->layout->disableLayout(); // Disable Layouts
        
        // setup database
    	$registry = Zend_Registry::getInstance();
        
        $select = $registry->db->select()
                               ->distinct()
    					       ->from(array('t' => 'tags'), 'tag_tag')
				   		       ->order('t.tag_tag DESC');
    					   
    	$this->view->tagArray = $registry->db->fetchall($select);

        // Then get the query, defaulting to an empty string
        $this->view->query = $this->_getParam('q', '');
    }
    
	/**
     * Tag Tab Action - count tags and update tab title
     */
    public function tabAction () {   
        
        $this->_helper->layout()->disableLayout(); // Disable layout
        $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
        // setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$type = $this->_request->getParam('type');
    	$slave = $this->_request->getParam('slave');
    	
    	$select = $registry->db->select()
    					   ->from(array('t' => 'tags'))
				   		   ->where('tag_slave = ?',$slave)
				   		   ->where('tag_type = ?',$type);
    					   
    	$tagArray = $registry->db->fetchall($select);
    	
    	echo 'Tags ('.count($tagArray).')';
    }
}