<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class Admin_AssetsController extends CMS_Controller_Action_Admin
{
    public function setLayout ()
    {
        // Change layout to Location
        $this->_helper->layout->setLayout('assets');
    }
    
    public function get_mime_type($filepath) {
        ob_start();
        system("file -i -b {$filepath}");
        $output = ob_get_clean();
        $output = explode("; ",$output);
        if ( is_array($output) ) {
            $output = $output[0];
        }
        return trim($output);
    }
    
    /**
     *  
     */
    public function assetType($mime) 
    {
        $images = array('image/jpeg', 'image/gif', 'image/png', 'image/bmp'); // Image File Types
        
        $videos = array('video/x-flv'); // Video File Types
        
        $audio = array('audio/mpeg'); // Audio File Types
        
        $flash = array('application/x-shockwave-flash'); // Flash File Types
        
        if (in_array($mime, $images)) :
            $type = 'image';
        elseif(in_array($mime, $videos)) :
            $type = 'video';
        elseif(in_array($mime, $audio)) :
            $type = 'audio';
        elseif(in_array($mime, $flash)) :
            $type = 'flash';
        else :
            $type = 'other';
        endif;
  
        return $type;
    } 
    
    /**
     * The default action - explore folder
     */
    public function indexAction ()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
        
            $this->setLayout(); // Set Layout
        
            $this->view->type = $this->_request->getParam('type');
            $this->view->folder = $this->_request->getParam('folder');
            $this->view->method = $this->_request->getParam('method');
            $this->view->field = $this->_request->getParam('field');
        
            if(!isset($this->view->folder)) :
                $this->view->folder = '0';
            endif;
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    if($this->view->folder != '0') : // If current folder is not root
    	
    	        // Build the query
    	        $select = $registry->db->select()
    	                           ->from(array('f' => 'assets_folders'))
    	                           ->where('f.folder_id = ?', $this->view->folder)
    	                           ->limit(1,0);

		        // Set the parent folder array
		        $parentArray = $registry->db->fetchall($select);

                $this->view->parentArray = $parentArray['0'];
        
            endif;
    	
    	    // Build the folder query
    	    $select = $registry->db->select()
    	                       ->from(array('f' => 'assets_folders'))
    	                       ->where('f.folder_parent = ?', $this->view->folder)
    	                       ->order('f.folder_name ASC');

		    // Set the data array
		    $this->view->folderArray = $registry->db->fetchall($select);

		    // Build the assets query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_folder = ?', $this->view->folder)
    	                       ->order('a.asset_name ASC');

		    // Set the data array
		    $this->view->assetArray = $registry->db->fetchall($select);
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    }
    
	/**
     * Upload action - show upload page
     */
    public function uploadAction ()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'fassetupload')) :
        
            $this->setLayout();
            $this->view->type = $this->_request->getParam('type');
            $this->view->folder = $this->_request->getParam('folder');
            $this->view->method = $this->_request->getParam('method');
            $this->view->field = $this->_request->getParam('field');
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    }
    
	/**
     * Download action - download asset
     */
    public function downloadAction ()
    {
        $this->_helper->layout->disableLayout(); // Disable Layouts
        $this->_helper->viewRenderer->setNoRender();
        
        
        $this->setLayout();
        $key = $this->_request->getParam('key');
        
        if(isset($key)) :
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_key = ?', $key)
    	                       ->limit(1,0);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);
		    
		    if(count($assetArray)) :

                $asset = $assetArray['0'];
        
                $file = $registry->assets->assets->syspath.$asset['asset_file'];
        
                if(file_exists($file)) :
                
                    $data = file_get_contents($file);
                    
                    $response = $this->getResponse();
                    $response->setHeader('Content-Disposition', 'attachment; filename='.urlencode($asset['asset_name']).'.'.$asset['asset_extension']);
                    $response->setHeader('Content-Type', $asset['asset_mime'], true);
                    $response->setHeader('Content-Length', $asset['asset_size'], true);
                    $response->setBody($data);
                    $response->sendResponse();
                    die();
        
                else:
                
                    $this->_forward('notfound','error','admin');
                    
                endif;
                
            else:
                
                $this->_forward('notfound','error','admin');
            
            endif;
           
        endif;
        
    }
    
	/**
     * Open action - open asset in browser
     */
    public function openAction ()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender();
        
        $this->setLayout();
        $key = $this->_request->getParam('key');
        
        if(isset($key)) :
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_key = ?', $key)
    	                       ->limit(1,0);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);
		    
		    if(count($assetArray)) :
		    
		        $asset = $assetArray['0'];
		    
                $file = $registry->assets->assets->syspath.$asset['asset_file'];
                
                if(file_exists($file)) :
       
                    $data = file_get_contents($file);
                    
                    $response = $this->getResponse();
                    $response->setHeader('Content-Type', $asset['asset_mime'], true);
                    $response->setHeader('Content-Disposition', 'inline; filename='.urlencode($asset['asset_name']).'.'.$asset['asset_extension']);
                    $response->setHeader('Content-Transfer-Encoding', 'binary'); 
                    $response->setHeader('Content-Length', $asset['asset_size'], true);
                    $response->setBody($data);
                    $response->sendResponse();
                    die();
            
                else:
                
                    $this->_forward('notfound','error','admin');
                    
                endif;
                
            else:
                
                $this->_forward('notfound','error','admin');
            
            endif;
           
        endif;
        
    }
    
	/**
     * Thumb action - render thumb in browser
     */
    public function thumbAction ()
    {
        $registry = Zend_Registry::getInstance();
        
        $this->_helper->layout->disableLayout();
        $this->_helper->ViewRenderer->setNoRender();
        
        $key = $this->_request->getParam('key');
        $width = $this->_request->getParam('width');
        $height = $this->_request->getParam('height');
        $type = $this->_request->getParam('type');
        
        if($type == NULL) :
            $type = 'adaptive';
        endif;
        
        $syspath = $registry->assets->assets->syspath;
        
        $defaultimage = $syspath.'images/default/default.png';
        
        if(isset($key)) :
        
            // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_key = ?', $key)
    	                       ->limit(1,0);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);
		    
		    if(count($assetArray)) :
		    
		        $asset = $assetArray['0'];
		        
		        if($this->AssetType($asset['asset_mime']) == 'image') :
		    
                    $file = $syspath.$asset['asset_file'];
                
                    if(file_exists($file)) :
                
                        // Set full path to thumbnail folder
                        $thumbfile = $syspath.'images/'.$width.'x'.$height.$type.$asset['asset_id'].'.'.$asset['asset_extension'];
                        
                        $filename = $asset['asset_id'].'.'.$asset['asset_extension'];
        
                        // If the thumb doesn't exist create it
                        if (!file_exists($thumbfile)) :
        
                            $thumb = PhpThumbFactory::create($file);

                            if ($type == 'adaptive') :
                                $thumb->adaptiveResize($width, $height);
                            elseif ($type == 'resize') :
                                $thumb->resize($width, $height);
                            endif;
                                
                            $thumb->save($thumbfile);
            
                        endif;
       
                    else :
                    
                        $defaultimage = $syspath.'images/default/notfound.png';
                        
                        $filename = 'notfound.png';
                        
                        // Set full path to thumbnail folder
                        $thumbfile = $syspath.'images/'.$width.'x'.$height.$type.$filename;
                        
                        // If the thumb doesn't exist create it
                        if (!file_exists($thumbfile)) :
        
                            $thumb = PhpThumbFactory::create($defaultimage);

                            $thumb->adaptiveResize($width, $height);
                            $thumb->save($thumbfile);
            
                        endif;
                    
                    endif;
            
                else:
                
                    $defaultimage = $syspath.'images/default/invalid.png';
                        
                        $filename = 'invalid.png';
                        
                        // Set full path to thumbnail folder
                        $thumbfile = $syspath.'images/'.$width.'x'.$height.$type.$filename;
                        
                        // If the thumb doesn't exist create it
                        if (!file_exists($thumbfile)) :
        
                            $thumb = PhpThumbFactory::create($defaultimage);

                            $thumb->adaptiveResize($width, $height);
                            $thumb->save($thumbfile);
            
                        endif;
                    
                endif;
                
            else:
                
                $defaultimage = $syspath.'images/default/notfound.png';
                        
                        $filename = 'notfound.png';
                        
                        // Set full path to thumbnail folder
                        $thumbfile = $syspath.'images/'.$width.'x'.$height.$type.$filename;
                        
                        // If the thumb doesn't exist create it
                        if (!file_exists($thumbfile)) :
        
                            $thumb = PhpThumbFactory::create($defaultimage);

                            $thumb->adaptiveResize($width, $height);
                            $thumb->save($thumbfile);
            
                        endif;
                    
            endif;
           
        else:
        
            $defaultimage = $syspath.'images/default/default.png';
                        
            $filename = 'default.png';
                        
            // Set full path to thumbnail folder
            $thumbfile = $syspath.'images/'.$width.'x'.$height.$type.$filename;
                        
            // If the thumb doesn't exist create it
            if (!file_exists($thumbfile)) :
        
                $thumb = PhpThumbFactory::create($defaultimage);

                $thumb->adaptiveResize($width, $height);
                $thumb->save($thumbfile);
            
            endif;
                    
        endif;
        
        $info = getimagesize($thumbfile);
        $mimeType = $info['mime'];
       
        $size = filesize($thumbfile);
                    
        $data = file_get_contents($thumbfile);
                    
        $response = $this->getResponse();
        $response->setHeader('Content-Type', $mimeType, true);
        $response->setHeader('Content-Disposition', 'inline; filename='.urlencode($filename));
        $response->setHeader('Content-Transfer-Encoding', 'binary'); 
        $response->setHeader('Content-Length', $size, true);
        $response->setBody($data);
        $response->sendResponse();
        die();
        
    }
    
	/**
     * Receive action - receive and process uploaded file
     */
    public function receiveAction ()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'fassetupload')) :
        
            $this->_helper->layout->disableLayout(); // Disable Layouts
            $this->_helper->viewRenderer->setNoRender();
        
            $folder = $this->_request->getParam('folder');
        
            $registry = Zend_Registry::getInstance();
        
            if (!empty($_FILES)) :
            
                $tempFile = $_FILES['Filedata']['tmp_name'];
	            $targetPath = $registry->assets->assets->syspath;
	            $targetFile = tempnam($targetPath, "up_");
	            $filePath = pathinfo($_FILES['Filedata']['name']);
	            $name = $filePath['filename'];
		        $extension = $filePath['extension'];
		        
		        // setup database
    	        $registry = Zend_Registry::getInstance();
    	
    	        // Build the query
    	        $select = $registry->db->select()
    	                           ->from(array('a' => 'assets'))
    	                           ->where('a.asset_name = ?', $name)
    	                           ->where('a.asset_extension = ?', $extension)
    	                           ->where('a.asset_folder = ?', $folder)
    	                           ->limit(1,0);

		        // Set the data array
		        $assetArray = $registry->db->fetchall($select);

                if(!count($assetArray)) :
	            
		            move_uploaded_file($tempFile, $targetFile);
		        
		            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') :
		                $finfo = finfo_open(FILEINFO_MIME_TYPE);
		                $mime = finfo_file($finfo, $targetFile);
		            else :
		                $mime = $this->get_mime_type($targetFile);
		            endif;
		            
		            $size = filesize($targetFile);
		            finfo_close($finfo);
    	
                    // Create our data array
                    $data = array(
                		'asset_folder'	    => $folder,
                        'asset_key'			=> md5(basename($targetFile)),
                		'asset_name'	    => $name,
                		'asset_extension'	=> $extension,
                		'asset_file'	    => basename($targetFile),
                		'asset_mime'		=> $mime,
                		'asset_size'		=> $size,
                		'asset_user'	    => $this->view->user->user_id,
                		'asset_date'		=> new Zend_Db_Expr('NOW()'),
                		'asset_modified'	=> new Zend_Db_Expr('NOW()')
                    );

                    // Insert data into database
                    $registry->db->insert('assets', $data);
                
		            echo '1';
		            
		        else :
	        
	                echo '0';
	        
                endif;
		        
	        else :
	        
	            echo '0';
	        
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    }
    
	/**
	 * Edit Asset - edit an asset
	 */
	public function assetAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'fassetedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->asset = $this->_request->getParam('asset');
	    
	        $registry = Zend_Registry::getInstance();
    	        
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_id = ?', $this->view->asset);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);
		
		    $this->assetArray = $assetArray['0'];
		
		    $this->view->filename = $this->assetArray['asset_name'];
		    $this->view->extension = $this->assetArray['asset_extension'];

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'filename' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Filename is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            // Build the query
    	            $select = $registry->db->select()
    	                               ->from(array('a' => 'assets'))
    	                               ->where('a.asset_folder = ?', $this->assetArray['asset_folder'])
    	                               ->where('a.asset_id != ?', $this->view->asset)
    	                               ->where('a.asset_name = ?', $input->filename);

		            // Set the data array
		            $parentArray = $registry->db->fetchall($select);
		    
		            if (count($parentArray)) :
		                $unique = 'false';
		            else :
		                $unique = 'true';
		            endif;
            
                    if ($unique == 'true') :
    	
                        // Create our data array
                        $data = array(
                			'asset_name'        => $input->filename,
                        	'asset_modified'    => new Zend_Db_Expr('NOW()')
                        );

                        // Insert data into database
                        $registry->db->update('assets', $data, 'asset_id = '.$this->view->asset);
                    
                        echo '<p class="Spacer"></p>
						<div class="cUpd">Asset Edited</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="asseteditcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide(); location.reload(true);">Close</button>
						</div>';
                    
                    else :
                
                        $this->view->posted = 'N';
                    
                        // Set Error Messages
                        $this->view->messages = array(array('duplicate' => 'An asset with this filename already exists in this folder'));
                    
                        $this->view->filename = $input->filename;
                
	                endif;
                
                 else :
                
                    $this->view->posted = 'N';
             
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->filename = $input->filename;
                
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
    
	/**
	 * Add New Folder - create a new folder
	 */
	public function foldernewAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'ffoldernew')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->parent = $this->_request->getParam('parent');

	        if($this->_request->isPost()) :
                
	            $options = array();
    
                $filters = array();

                $validators = array(
                	'foldertitle' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Folder name is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            $registry = Zend_Registry::getInstance();
    	        
    	            // Build the query
    	            $select = $registry->db->select()
    	                               ->from(array('f' => 'assets_folders'))
    	                               ->where('f.folder_parent = ?', $this->view->parent)
    	                               ->where('f.folder_name = ?', $input->foldertitle);

		            // Set the data array
		            $folderArray = $registry->db->fetchall($select);
		    
		            if (count($folderArray)) :
		                $unique = 'false';
		            else :
		                $unique = 'true';
		            endif;
            
                    if ($unique == 'true') :
    	
                        // Create our data array
                        $data = array(
                        	'folder_parent'	 => $this->view->parent,
                			'folder_name'    => $input->foldertitle,
                    		'folder_date'	 => new Zend_Db_Expr('NOW()'),
                    		'folder_modify'	 => new Zend_Db_Expr('NOW()')
                        );

                        // Insert data into database
                        $registry->db->insert('assets_folders', $data);
                    
                        echo '<p class="Spacer"></p>
						<div class="cUpd">Folder Created</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="foldernewcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide(); location.reload(true);">Close</button>
						</div>';
                    
                    else :
                
	                    $this->view->posted = 'N';
                    
                        // Set Error Messages
                        $this->view->messages = array(array('duplicate' => 'A folder with this name already exists in this folder'));
                    
                        $this->view->foldertitle = $input->foldertitle;
                
	                endif;
                
                 else :
            
                    $this->view->posted = 'N';
                
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->foldertitle = $input->foldertitle;
                        
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
          else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Delete Folder action
	 */
	public function folderdeleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'ffolderdelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->folder = $this->_getParam('folder');
        
            if ($this->view->confirm == '1' & isset($this->view->folder)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Build the query
    	        $select = $registry->db->select()
    	                           ->from(array('a' => 'assets'))
    	                           ->where('a.asset_folder = ?', $this->view->folder);

		        // Set the data array
		        $assetArray = $registry->db->fetchall($select);
		        
		        // Build the query
    	        $select = $registry->db->select()
    	                           ->from(array('f' => 'assets_folders'))
    	                           ->where('f.folder_parent = ?', $this->view->folder);

		        // Set the data array
		        $folderArray = $registry->db->fetchall($select);
		        
		        if(!count($folderArray) && !count($assetArray)) :
    	    
    	            // Delete the asset from db
		            $registry->db->delete('assets_folders', 'folder_id = '.$this->view->folder);
		    
		            echo '<p class="Spacer"></p>
						<div class="cUpd">Folder Deleted</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="folderdeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
		            
		        else :
		        
		             echo '<p class="Spacer"></p>
						<div class="cErr">Folders With Contents Cannot Be Deleted</div>
						<p class="Spacer"></p>
						<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="folderdeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
						</div>';
		             
		        endif;
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to delete this folder?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="folderdeleteButton" value="Delete" iconClass="deletecategoryIcon" onClick="getDialog(\'/admin/assets/folderdelete/folder/'.$this->view->folder.'/confirm/1/\',\'Delete Folder\');">Delete</button>
						<button dojoType="dijit.form.Button" type="button" id="folderdeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Edit Folder - edit a folder
	 */
	public function folderAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'ffolderedit')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->folder = $this->_request->getParam('folder');
	    
	        $registry = Zend_Registry::getInstance();
    	        
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('f' => 'assets_folders'))
    	                       ->where('f.folder_id = ?', $this->view->folder);

		    // Set the data array
		    $folderArray = $registry->db->fetchall($select);
		
		    $this->folderArray = $folderArray['0'];
		
		    $this->view->foldername = $this->folderArray['folder_name'];

	        if($this->_request->isPost()) :
                
	            $options = array();

                $filters = array();

                $validators = array(
                	'foldertitle' => array(
                		'presence' => 'required',
                		'NotEmpty',
                		'messages' => array(array( Zend_Validate_NotEmpty::IS_EMPTY => "Folder name is required"))
                    )
                );

                $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
                if ($input->isValid()) :
            
    	            // Build the query
    	            $select = $registry->db->select()
    	                               ->from(array('f' => 'assets_folders'))
    	                               ->where('f.folder_parent = ?', $this->folderArray['folder_parent'])
    	                               ->where('f.folder_id != ?', $this->view->folder)
    	                               ->where('f.folder_name = ?', $input->foldertitle);

		            // Set the data array
		            $parentArray = $registry->db->fetchall($select);
		    
		            if (count($parentArray)) :
		                $unique = 'false';
		            else :
		                $unique = 'true';
		            endif;
            
                    if ($unique == 'true') :
    	
                        // Create our data array
                        $data = array(
                			'folder_name'    => $input->foldertitle,
                        	'folder_modify'	 => new Zend_Db_Expr('NOW()')
                        );

                        // Insert data into database
                        $registry->db->update('assets_folders', $data, 'folder_id = '.$this->view->folder);
                    
                        echo '<p class="Spacer"></p>
						<div class="cUpd">Folder Edited</div>
						<p class="Spacer"></p>
                    	<div class="cFormDS">
							<button dojoType="dijit.form.Button" type="button" id="foldereditcloseButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide(); location.reload(true);">Close</button>
						</div>';
                    
                    else :
                
	                    $this->view->posted = 'N';
                    
                        // Set Error Messages
                        $this->view->messages = array(array('duplicate' => 'A folder with this name already exists in this folder'));
                    
                        $this->view->foldername = $input->foldertitle;
                
	                endif;
                
                 else :
            
                    $this->view->posted = 'N';
             
                    // Set Error Messages
                    $this->view->messages = $input->getMessages();
                
                    $this->view->foldername = $input->foldertitle;
                        
                endif;
             
             else :
         
                 $this->view->posted = 'N';
         
             endif;
         
         else :
		
		    $this->_forward('privileges','error','admin');
		
		 endif;
	}
	
	/**
	 * Folder Properties - folder properties
	 */
	public function folderpropertiesAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->folder = $this->_request->getParam('folder');
	    
	        $registry = Zend_Registry::getInstance();
    	        
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('f' => 'assets_folders'))
    	                       ->where('f.folder_id = ?', $this->view->folder);

		    // Set the data array
		    $folderArray = $registry->db->fetchall($select);
		
		    $this->view->folderArray = $folderArray['0'];
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Delete action
	 */
	public function deleteAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets') & $this->view->acl->isAllowed($this->view->user->user_role, 'fassetdelete')) :
	    
	        $this->_helper->layout()->disableLayout(); // Disable layout
            $this->_helper->viewRenderer->setNoRender(true); // Disable view
        
            $this->view->confirm = $this->_getParam('confirm');
            $this->view->asset = $this->_getParam('id');
        
            if ($this->view->confirm == '1' & isset($this->view->asset)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	    
    	        // Build the query
    	        $select = $registry->db->select()
    	                           ->from(array('a' => 'assets'))
    	                           ->where('a.asset_id = ?', $this->view->asset)
    	                           ->limit(1,0);

		        // Set the data array
		        $assetArray = $registry->db->fetchall($select);
    	    
    	        $asset = $assetArray['0'];
		    
                $file = $registry->assets->assets->syspath.$asset['asset_file'];
    	    
    	        // Delete File
                unlink($file);
    	    
    	        // Delete the asset from db
		        $registry->db->delete('assets', 'asset_id = '.$this->view->asset);
		    
		        echo '<p class="Spacer"></p>
					<div class="cUpd">Asset Deleted</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="assetdeletecancelButton" value="Close" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();location.reload(true);">Close</button>
					</div>';
		    
	        else :
	        
	            echo '<p class="Spacer"></p>
					<div class="cUpd">Are you sure you want to delete this asset?</div>
					<p class="Spacer"></p>
					<div class="cFormDS">
						<button dojoType="dijit.form.Button" type="button" id="assetdeleteButton" value="Delete" iconClass="deletefileIcon" onClick="getDialog(\'/admin/assets/delete/id/'.$this->view->asset.'/confirm/1/\',\'Delete Asset\');">Delete</button>
						<button dojoType="dijit.form.Button" type="button" id="assetdeletecancelButton" value="Cancel" iconClass="cancelIcon" onClick="dijit.byId(\'ajaxDialog\').hide();">Cancel</button>
					</div>';
	        
	        endif;
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Asset Properties - asset properties
	 */
	public function propertiesAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->asset = $this->_request->getParam('asset');
	    
	        $registry = Zend_Registry::getInstance();
    	        
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_id = ?', $this->view->asset);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);
		
		    $this->view->assetArray = $assetArray['0'];
		
		else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
	 * Image Select - select and insert image for editor
	 */
	public function imageinsertAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->key = $this->_request->getParam('key');
	    
	        // setup database
    	    $registry = Zend_Registry::getInstance();
    	
    	    // Build the query
    	    $select = $registry->db->select()
    	                       ->from(array('a' => 'assets'))
    	                       ->where('a.asset_key = ?', $this->view->key)
    	                       ->limit(1,0);

		    // Set the data array
		    $assetArray = $registry->db->fetchall($select);
		    
		    $asset = $assetArray['0'];
		
		    $syspath = $registry->assets->assets->syspath;
		
		    $file = $syspath.$asset['asset_file'];
		
		    $size = getimagesize($file);
        
            return $this->view->size = array('width' => $size['0'],'height' => $size['1']);
		
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
	/**
     * Thumb action - render thumb in browser
     */
    public function imagepreviewAction ()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
        
            $this->_helper->layout->disableLayout();
        
            $this->view->key = $this->_request->getParam('key');
            $this->view->width = $this->_request->getParam('width');
            $this->view->height = $this->_request->getParam('height');
            $this->view->type = $this->_request->getParam('type');
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    }
    
	/**
     * Thumb action - render thumb in browser
     */
    public function thumbpreviewAction ()
    {
        if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
        
            $registry = Zend_Registry::getInstance();
        
            $this->_helper->layout->disableLayout();
            $this->_helper->ViewRenderer->setNoRender();
        
            $key = $this->_request->getParam('key');
            $width = $this->_request->getParam('width');
            $height = $this->_request->getParam('height');
            $type = $this->_request->getParam('type');
        
            if($type == NULL) :
                $type = 'resize';
            endif;
        
            $syspath = $registry->assets->assets->syspath;
        
            if(isset($key)) :
        
                // setup database
    	        $registry = Zend_Registry::getInstance();
    	
    	        // Build the query
    	        $select = $registry->db->select()
    	                           ->from(array('a' => 'assets'))
    	                           ->where('a.asset_key = ?', $key)
    	                           ->limit(1,0);

		        // Set the data array
		        $assetArray = $registry->db->fetchall($select);
		    
		        if(count($assetArray)) :
		    
		            $asset = $assetArray['0'];
		        
		            if($this->AssetType($asset['asset_mime']) == 'image') :
		    
                        $file = $syspath.$asset['asset_file'];
                
                        if(file_exists($file)) :
                
                            // Set full path to thumbnail folder
                            $thumbfile = $syspath.'images/'.$width.'x'.$height.$type.$asset['asset_id'].'.'.$asset['asset_extension'];
                        
                            $filename = $asset['asset_id'].'.'.$asset['asset_extension'];
        
                            $thumb = PhpThumbFactory::create($file);

                            if ($type == 'adaptive') :
                                $thumb->adaptiveResize($width, $height);
                            elseif ($type == 'resize') :
                                $thumb->resize($width, $height);
                            elseif ($type == 'crop') :
                                $thumb->cropFromCenter($width, $height);
                            endif;
                                
                            $thumb->show();
            
                        endif;
            
                    endif;
                
                endif;
           
            endif;
        
        else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
    }
    
	/**
	 * Link Insert - select and insert link for editor
	 */
	public function linkinsertAction() 
	{
	    if($this->view->acl->isAllowed($this->view->user->user_role, 'fassets')) :
	    
	        $this->_helper->layout->disableLayout(); // Disable Layouts
	    
	        $this->view->key = $this->_request->getParam('key');
	    
	    else :
		
		    $this->_forward('privileges','error','admin');
		
		endif;
	}
	
public function testAction()
	{
	    $this->_helper->layout->disableLayout(); // Disable Layouts
	}
	
}