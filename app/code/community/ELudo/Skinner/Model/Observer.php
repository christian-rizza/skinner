<?php

/**
 * News module observer
 * @author Magento
 */
class ELudo_Skinner_Model_Observer {

    public function beforeSkinnerDisplayed(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('eludo_skinner')->isEnabled()) {
           return $this;
        }
        
       /* @var $block Mage_Page_Block_Html_Head */
       $block = $observer->getEvent()->getBlock();
       
       if ("head" == $block->getNameInLayout()) {
           
           $block->addJs(Mage::helper('eludo_skinner')->getJQueryPath());
           $block->addJs(Mage::helper('eludo_skinner')->getJQueryUIPath());
       }
 
       return $this;
    }
}
            