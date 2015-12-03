<?php

class ELudo_Skinner_Block_Product_View extends Mage_Catalog_Block_Product_View {
    
    protected function _construct()
    {
        parent::_construct();
        
        $this->setTemplate('sales/order/info.phtml');
    }
    
    public function isSkinned()
    {
        $attributeSetModel = Mage::getModel("eav/entity_attribute_set");
        $attributeSetModel->load($this->getProduct()->attribute_set_id);
        $attributeSetName = $attributeSetModel->getAttributeSetName();
        
        if ($attributeSetName=="Skinnable")
        {
            return true;
        }

        return false;
    }
    
    public function getSkinnerUrl()
    {
        Mage::app()->getCacheInstance()->clean('all');
        return Mage::getUrl('skinner/', array('id' => $this->getProduct()->getId()));
    }
    
}