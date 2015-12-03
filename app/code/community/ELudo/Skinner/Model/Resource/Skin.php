<?php
 
class ELudo_Skinner_Model_Resource_Skin extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('eludo_skinner/skin', 'skin_id');
    }
}