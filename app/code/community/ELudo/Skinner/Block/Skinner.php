<?php
/**
 * News List block
 *
 * @author Magento
 */
class ELudo_Skinner_Block_Skinner extends Mage_Core_Block_Template
{
    public function __construct(array $args = array()) {
        
        parent::__construct($args);
    }
    
    public function getImage()
    {
        //get images from backoffice
        $collection = Mage::getModel("eludo_skinner/image")->getCollection();
        $result = $collection->getData();
        
        //get images from user logged
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $dir = dir($img_path = Mage::getBaseDir('media') . DS . "skinner");
        while (false!== ($file = $dir->read())) //Reads Directory
        {
            $prefix = substr($file, 0, strlen("user-")+strlen($customer_id));
            if ($prefix=="user-".$customer_id) 
            {
                $filesall["title"] = $file; // Store in Array
                $filesall["image"] = $file; // Store in Array
                
                $result[] = $filesall;
            }
        }
        $dir->close(); // Close Directory
        
        
        
        asort($result); // Sorts the Array
        
        return $result;
    }
    
    public function getText()
    {
        $collection = Mage::getModel("eludo_skinner/text")->getCollection();
        return $collection->getData();
    }
    
    public function getProductId()
    {
        return $this->getRequest()->getParam('id');
    }
    
    public function getProduct()
    {
        $itemId = $this->getRequest()->getParam('id');
        
        $collection = Mage::getModel("catalog/product");
        $collection->load($itemId);
        
        $attributes = array ();
        $attr = array("skinned_front","skinned_back","skinned_left","skinned_right");
        foreach ($attr as $value) {
            
            $label = $collection->getResource()->getAttribute($value)->getFrontendlabel();
            $val = $collection->getResource()->getAttribute($value)->getFrontend()->getValue($collection);
            
            $a = new SimpleXMLElement($val);
            $val = $a['href'];
            
            $item = array('name'=>$label, "value"=>$val);
            $attributes[] = $item;
        }
        
                
        return $attributes;
    }
    
    public function getUserImageUrl($image_name)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."skinner/".$image_name;
        
    }
    
    public function getImageUrl($image_name)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."skins/".$image_name;
        
    }
    public function getFormKey()
    {
        return Mage::getSingleton('core/session')->getFormKey();
    }
    
}

