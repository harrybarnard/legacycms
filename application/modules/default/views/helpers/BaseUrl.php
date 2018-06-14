<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */

class Zend_View_Helper_BaseUrl
{
	function baseUrl()
	{
		$fc = Zend_Controller_Front::getInstance();
		return $fc->getBaseUrl();
	}
}