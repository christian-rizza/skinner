<?php
 
class ELudo_Skinner_Model_Resource_Text_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() {
        
       $this->_init('eludo_skinner/text');
       
    }
}