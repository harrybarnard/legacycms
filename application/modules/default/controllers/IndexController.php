<?php
/**
 *
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class IndexController extends CMS_Controller_Action_Default
{
    public function init()
    {
        // Get registry
    	$registry = Zend_Registry::getInstance();
        
        // load assets configuration
        $articles = new Zend_Config_Ini('../application/configs/articles.ini', array('config'));
        $registry->set('articles', $articles);
        
        $youtube = new Zend_Config_Ini('../application/configs/youtube.ini', array('config'));
        $registry->set('youtube', $youtube);
        
        $this->registry = Zend_Registry::getInstance();
    }
	
	/**
	 * The default action - show the home page
	 */
    public function indexAction() 
    {
        $this->setRefer();
        
        $this->view->params = $this->_request->getParams();
        
		// setup database
    	$registry = Zend_Registry::getInstance();
    	
    	$select = $registry->db->select()
    					   ->from(array('a' => 'articles'),array('a.*','article_day' => 'DAY(a.article_published)','article_month' => 'MONTH(a.article_published)','article_year' => 'YEAR(a.article_published)'))
				   		   ->join(array('c' => 'articles_categories'),'a.article_category = c.acat_id',array('c.acat_title'))
				   		   ->join(array('u' => 'users'),'a.article_user = u.user_id',array('u.user_alias'))
				   		   ->where('article_status = ?','published')
    					   ->order(array('a.article_sticky DESC','a.article_published DESC'))
    					   ->limit(5,0);

    	// Set the data array
		$this->view->articlesArray = $registry->db->fetchall($select);
		
    }
    
	/**
     * Tags action - list all tags
     */
    public function tagsAction ()
    {
    	$this->setRefer();
    }
    
	/**
     * The search action - search the site search index
     */
    public function searchAction ()
    {
        $this->view->query = strip_tags($this->_request->getParam('query'));
        
        if(isset($this->view->query)) :
            // setup database
  	        $registry = Zend_Registry::getInstance();
  	    
            $page = $this->_getParam('page');
    	    if($page != NULL) {
    		    $this->view->page = $page;
    	    } else {
    		    $this->view->page = 1;
    	    }
        
            //open the index
            $index = Zend_Search_Lucene::open($registry->search->search->syspath.'site-index');
            
            $searchphrase = strtolower($this->view->query);
            
            $phrases = explode(" ", $searchphrase);
            
            $query = new Zend_Search_Lucene_Search_Query_Boolean();
            
            $subquery1 = new Zend_Search_Lucene_Search_Query_Phrase();
            foreach($phrases as $phrase) :
                $subquery1->addTerm(new Zend_Search_Lucene_Index_Term($phrase), null);
            endforeach;
            $subquery1->setSlop(3);
      
            $to = new Zend_Search_Lucene_Index_Term($this->_helper->makeDate('Ymd'), 'date');
            $subquery2 = new Zend_Search_Lucene_Search_Query_Range(null, $to, true);

            $query->addSubquery($subquery1, true);
            $query->addSubquery($subquery2, true);

            $hits  = $index->find($query);
            
            //Create Paginator Object
		    $paginator = Zend_Paginator::factory($hits);
		    $paginator->setCurrentPageNumber($this->view->page);
		    $paginator->setItemCountPerPage(6);
		    $paginator->setPageRange(5);
		
		    //Save Paginator as View var.
		    $this->view->hits = $paginator;
		
		else : // Else if query not set redirect to 404 Error
        
            $this->_helper->layout->setLayout('main');	    
            $this->_forward('notfound','error','default');
            
        endif;

    }
    
	/**
     * Contact action - display contact page
     */
    public function contactAction ()
    {    
        $this->setRefer();
        
        $this->view->captcha = new Zend_Captcha_Image(array(  
        	'wordLen' => 7,  
			'font' => './_captcha/fonts/Envy Code R.ttf',
        	'fontSize' => 22,  
			'imgDir' => './_captcha/images/',  
			'imgUrl' => '/_captcha/images/',  
			'width' => 180,  
			'height' => 60,  
			'dotNoiseLevel' => 35,  
			'lineNoiseLevel' => 2    
        )); 
        
        $registry = Zend_Registry::getInstance();
        
        if($this->_request->isPost()) // If form has been posted
        {
            $options = array();

            $filters = array(
       			'content' => array('StringTrim', 'StripTags')
            );

            $validators = array(
                'name' => array(
                    'NotEmpty',
                	'messages' => array(
                        'Missing Name - you must enter your name',
                    ),
                	'breakChainOnFailure' => true
                ),
                'email' => array(
                	'presence' => 'required',
                	'NotEmpty',
                    'EmailAddress',
                	'messages' => array(array(Zend_Validate_NotEmpty::IS_EMPTY => "Missing E-mail Address - you did not enter an e-mail address"),array(Zend_Validate_EmailAddress::INVALID => "Invalid E-mail Address")),
                    'breakChainOnFailure' => true
                ),
                'subject' => array(
                    'NotEmpty',
                	'messages' => array(
                        'Missing Subject - you did not enter a message subject',
                    ),
                	'breakChainOnFailure' => true
                ),
                'message' => array(
                    'NotEmpty',
                	'messages' => array(
                        'Missing Message - you did not enter a message',
                    ),
                	'breakChainOnFailure' => true
                )
           );

           $input = new Zend_Filter_Input($filters, $validators, $_POST, $options);
            
           if ($input->isValid() & $this->view->captcha->isValid($_POST['captcha'])) // If the form input validates
           {

               Zend_Mail::setDefaultFrom($input->email, $input->name);
               Zend_Mail::setDefaultReplyTo($input->email, $input->name);
			   
			   $mail = new Zend_Mail();
               $mail->setBodyText($input->message);
               $mail->setFrom($input->email, $input->name);
               $mail->addTo($registry->site->site->email, $registry->site->site->recipient);
               $mail->setSubject('From: '.$input->email.' - '.$input->subject);
               $mail->send($registry->mail);
			   
			   Zend_Mail::clearDefaultFrom();
               Zend_Mail::clearDefaultReplyTo();
               
               $this->view->posted = 'Y';
                        
           } else { // If input is invalid
                        
               $this->view->messages = $input->getMessages(); // Pass Messages to view
               $this->view->email = $input->email; // Set value of email to match input
               $this->view->name = $input->name; // Set value of name to match input
               $this->view->subject = $input->subject; // Set value of subject to match input
               $this->view->message = $input->message; // Set value of message to match input
               
               if(!$this->view->captcha->isValid($_POST['captcha'])) :
                   $this->view->messages[] = array('captcha' => 'Invalid Verification Code - you must enter the code exactly as displayed');
               endif;
                        
           }
        }
        
        $this->view->id = $this->view->captcha->generate();
    }
}