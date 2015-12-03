<?php

class ELudo_Skinner_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'eludo/skinner/';
    const JQUERY_DIR = 'jquery/';
    const JQUERY_DIR_UI = 'jquery-ui-1.11.0.custom/';
    const JQUERY_FILE_NAME = 'jquery-1.11.0.min.js';
    const JQUERYUI_FILE_NAME = 'jquery-ui.min.js';
    const FILESTYLE_FILE_NAME = 'filestyle/jquery-filestyle.min.js';
    const XML_PATH_ENABLED = 'skinner/view/enabled';
    
    protected $_skinItemInstance;

    
    /**
     * Checks whether news can be displayed in the frontend
     *
     * @param integer|string|Mage_Core_Model_Store $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return true;
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }
    public function getSkinItemInstance()
    {
        if (!$this->_skinItemInstance) {
            $this->_skinItemInstance = Mage::registry('skinner_item');

            if (!$this->_skinItemInstance) {
                //Mage::throwException($this->__('News item instance does not exist in Registry'));
            }
        }
        
        return $this->_skinItemInstance;
    }
    /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . self::JQUERY_DIR . self::JQUERY_FILE_NAME;
    }
    public function getJQueryUIPath($file)
    {
        return self::NAME_DIR_JS . self::JQUERY_DIR_UI . self::JQUERYUI_FILE_NAME;
    }
    public function getJQueryFileStylePath($file)
    {
        return self::NAME_DIR_JS . self::FILESTYLE_FILE_NAME;
    }
    
    public function getBasePath()
    {
        return Mage::getBaseUrl();
    }
    
}
