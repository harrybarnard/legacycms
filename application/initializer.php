<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

require_once 'Zend/Controller/Plugin/Abstract.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Abstract.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Loader/Autoloader.php';

/**
 * 
 * Initializes configuration depending on the type of environment
 * (test, development, production, etc.)
 *  
 * This can be used to configure environment variables, databases, 
 * layouts, routers, helpers and more
 *   
 */
class Initializer extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Config
     */
    protected static $_config;

    /**
     * @var string Current environment
     */
    protected $_env;

    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    /**
     * @var string Path to application root
     */
    protected $_root;

    /**
     * Constructor
     *
     * Initialize environment, root path, and configuration.
     * 
     * @param  string $env 
     * @param  string|null $root 
     * @return void
     */
    public function __construct($env, $root = null)
    {
        $this->_setEnv($env);
        if (null === $root) {
            $root = realpath(dirname(__FILE__) . '/../');
        }
        $this->_root = $root;

        $this->initPhpConfig();
        
        $this->_front = Zend_Controller_Front::getInstance();
        
        // Set up autoload.
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->setFallbackAutoloader(true);
        
        // set the test environment parameters
        if ($env == 'development') {
            // Enable all errors so we'll know when something goes wrong.
			error_reporting(E_ALL | E_STRICT);
			ini_set('display_startup_errors', 1);
			ini_set('display_errors', 1);

			$this->_front->throwExceptions(true);
			$loader->suppressNotFoundWarnings(false);
        } else {
            // Suppress errors
            error_reporting(0);
            $this->_front->throwExceptions(false);
            $loader->suppressNotFoundWarnings(true);
        }
        
        // Get the registry
        $registry = Zend_Registry::getInstance();
        
        // load general configuration
        $config = new Zend_Config_Ini('../application/configs/config.ini', array($env));
        $registry->set('config', $config);
        
        // load site configuration
        $site = new Zend_Config_Ini('../application/configs/config.ini', array('site'));
        $registry->set('site', $site);
        
        // load search configuration
        $search = new Zend_Config_Ini('../application/configs/config.ini', array('search'));
        $registry->set('search', $search);
        
        // load assets configuration
        $assets = new Zend_Config_Ini('../application/configs/config.ini', array('assets'));
        $registry->set('assets', $assets);
        
        // Setup SMTP
        $config = array('auth' => 'login',
		                'register' => true,
						'port' => 465,
						'ssl' => 'ssl',
                		'username' => $registry->site->site->email,
                		'password' => $registry->site->smtp->pass);
 
        $transport = new Zend_Mail_Transport_Smtp($registry->site->smtp->server, $config);
        
        $registry->set('mail', $transport);

        // Set default locale
        $locale = new Zend_Locale('en_GB');
        Zend_Registry::set('Zend_Locale', $locale);
        
        $front = array(
            'lifetime' => 10,
            'automatic_serialization' => true
        );

        $back = array(
            'cache_dir' => '../cache'
        );

        $cache = Zend_Cache::factory('Core', 'File', $front, $back);

        Zend_Registry::set('cache', $cache);
        
    }

    /**
     * Initialize environment
     * 
     * @param  string $env 
     * @return void
     */
    protected function _setEnv($env) 
    {
		$this->_env = $env;    	
    }
    

    /**
     * Initialize Data bases
     * 
     * @return void
     */
    public function initPhpConfig()
    {
    }
    
    /**
     * Route startup
     * 
     * @return void
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
       	$this->initDb();
        $this->initHelpers();
        $this->initView();
        $this->initPlugins();
        $this->initRoutes();
        $this->initControllers();
    }
    
    /**
     * Initialize data bases
     * 
     * @return void
     */
    public function initDb()
    {
        // Get the registry
        $registry = Zend_Registry::getInstance();
        
        // setup database
        $db = Zend_Db::factory($registry->config->resources->db);
        Zend_Db_Table::setDefaultAdapter($db);
        $registry->set('db', $db);
    }

    /**
     * Initialize action helpers
     * 
     * @return void
     */
    public function initHelpers()
    {
    	// register global action helpers
    	Zend_Controller_Action_HelperBroker::addPath('../application/helpers', 'Zend_Controller_Action_Helper');
    	// register default action helpers
    	Zend_Controller_Action_HelperBroker::addPath('../application/modules/default/helpers', 'Zend_Controller_Action_Helper');
    }
    
    /**
     * Initialize view 
     * 
     * @return void
     */
    public function initView()
    {
		// Bootstrap layouts
		Zend_Layout::startMvc(array(
		    'layoutPath' => $this->_root .  '/application/layouts',
		    'layout' => 'main'
		));
    	
    }
    
    /**
     * Initialize plugins 
     * 
     * @return void
     */
    public function initPlugins()
    {
        // Get front controller        
        $front = Zend_Controller_Front::getInstance();
        // Custom Error Handler
        $front->registerPlugin( new CMS_Controller_Plugin_ErrorSelector() );
    }
    
    /**
     * Initialize routes
     * 
     * @return void
     */
    public function initRoutes()
    {
        // Get the routes from config.ini
        $router = Zend_Controller_Front::getInstance()->getRouter();
        $config = new Zend_Config_Ini('../application/configs/config.ini', array('routes'));
        $router->addConfig($config, 'routes');
    }

    /**
     * Initialize Controller paths 
     * 
     * @return void
     */
    public function initControllers()
    {
    	$this->_front->addModuleDirectory($this->_root . '/application/modules'); // Adds all modules and their controllers in directory
    }
    
}
