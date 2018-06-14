<?php
/**
 * LEGACY CMS
 * @author Harry Barnard
 * @version 2.1
 * @copyright Copyright (c) 2010 Harry Barnard (http://harrybarnard.com)
 */
require_once 'Zend/View/Interface.php';
/**
 * ArtCategoryList helper
 *
 * @uses viewHelper Zend_View_Helper
 */
class Zend_View_Helper_CountriesOptions
{
    /**
     * @var Zend_View_Interface 
     */
    public $view;
    /**
     *  
     */
    public function countriesOptions()
    {
        $locale = new Zend_Locale('en_GB');

        $countries = ($locale->getTranslationList('Territory', 'en', 2));
        asort($countries, SORT_LOCALE_STRING);

        foreach ($countries as $key => $name) {
            if($key != 'ZZ') {
        	    echo "<option value='".$key."'>".$name."</option>\n";
            }
        }
    }
    /**
     * Sets the view field 
     * @param $view Zend_View_Interface
     */
    public function setView (Zend_View_Interface $view)
    {
        $this->view = $view;
    }
}
