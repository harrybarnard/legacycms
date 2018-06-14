<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

// Bootstrap App

defined('APPLICATION_PATH')
|| define('APPLICATION_PATH',
    realpath(dirname(__FILE__) . '/../application'));

defined('APPLICATION_ENV')
|| define('APPLICATION_ENV',
    (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
        : 'production'));

// Autoload Composer
require __DIR__ . '/../vendor/autoload.php';

set_include_path('.' . PATH_SEPARATOR . '../library' . PATH_SEPARATOR . '../application/models' . PATH_SEPARATOR . get_include_path());

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/config.ini'
);

$application->getBootstrap()->bootstrap(array('db'));

$db = $application->getBootstrap()->getResource('db');
$config = new Zend_Config_Ini(__DIR__ . '/../application/configs/config.ini');

// Create Search Index

$index = Zend_Search_Lucene::create($config->search->search->syspath . 'site-index');
$index->commit();

// Create Super Admin User

function generatePassword()
{
    $chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;

    while ($i <= 7) {
        $num = rand() % 57;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}

try {
    $opts = new Zend_Console_Getopt(array(
        'email|e=s'    => 'user email address',
        'password|p=s' => 'user password'
    ));
    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    exit;
}


$salt = generatePassword();

$password = $config->site->site->key.$opts->getOption('p').$salt;

$string = $opts->getOption('e').$opts->getOption('p');

$key = md5($opts->getOption('e').$opts->getOption('p'));

// Get user data together
$data = array(
    'user_alias'	        => 'admin',
    'user_email'	        => $opts->getOption('e'),
    'user_role'	            => 3,
    'user_password'		    => md5($password),
    'user_key'	            => $key,
    'user_salt'             => $salt,
    'user_date'		        => new Zend_Db_Expr('NOW()'),
    'user_mailformat'       => 'text',
    'user_status'	        => 'active'
);

$db->insert('users', $data);

$userid = $db->lastInsertId();

$profiledata = array(
    'upro_userid'       => $userid,
    'upro_first'        => 'Super',
    'upro_last'         => 'Admin',
    'upro_country'      => 'GB',
    'upro_date'		    => new Zend_Db_Expr('NOW()')
);

$db->insert('users_profiles', $profiledata);

echo "Installation Complete!\n";