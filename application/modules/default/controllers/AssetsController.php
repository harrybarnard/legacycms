<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'PHPThumb/ThumbLib.inc.php';

class AssetsController extends CMS_Controller_Action_Default
{
    
    public function setLayout()
	{
		// Change to Articles layout
    	$this->_helper->layout->setLayout('articles');	
	}
	
    /**
     *  
     */
    public function assetType($mime) 
    {
        $images = array('image/jpeg', 'image/gif', 'image/png', 'image/bmp'); // Image File Types
        
        $videos = array('video/x-flv'); // Video File Types
        
        if (in_array($mime, $images)) :
            $type = 'image';
        elseif(in_array($mime, $videos)) :
            $type = 'video';
        else :
            $type = 'other';
        endif;
  
        return $type;
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
                
                    $this->_forward('notfound','error','default');
                    
                endif;
                
            else:
                
                $this->_forward('notfound','error','default');
            
            endif;
           
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
                
                    $this->_forward('notfound','error','default');
                    
                endif;
                
            else:
                
                $this->_forward('notfound','error','default');
            
            endif;
           
        endif;
        
    }
    
}