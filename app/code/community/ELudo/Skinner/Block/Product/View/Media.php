<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Simple product data view
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ELudo_Skinner_Block_Product_View_Media extends Mage_Catalog_Block_Product_View_Media
{
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
    
    public function getSkinnerImage()
    {
        $product_id = $this->getProduct()->getId();
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        if ($product_id && $customer_id)
        {
            $img_path = Mage::getBaseDir('media') . DS . "skinner";
            $img_path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
            $img_path.= "skinner/prod-".$customer_id."-".$product_id.".png";
            
            return $img_path;
        }
        else
        {
            return null;
        }
    }
}
