<?php

/**
 * News frontend controller
 *
 * @author E-Ludo Interactive
 */
class ELudo_Skinner_IndexController extends Mage_Core_Controller_Front_Action {

    public function testAttributeAction()
    {
        $set_created = false;
        $set_skeleton = -1;
        
        $set_name = "Skinnable";
        $skeleton_name = "Default";
                
        $model = Mage::getModel('eav/entity_attribute_set');
        $entityTypeID = Mage::getModel('catalog/product')->getResource()->getTypeId();
        
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityTypeID);
        foreach($collection as $coll){
            if ($coll->getAttributeSetName() == $set_name) {
                $set_created = true;
            }
            if ($coll->getAttributeSetName() == $skeleton_name) {
                $set_skeleton = $coll->getAttributeSetId();
            }
        }
        
        if (!$set_created)
        {
            //create attribute set
            
            $model->setEntityTypeId($entityTypeID);
            $model->setAttributeSetName($set_name);
            $model->validate();
            $model->save();
            
            $set_id = $model->getId();
            
            //set skeleton attribute set
            if ($set_skeleton!==-1)
            {
                $modelGroup = Mage::getModel('eav/entity_attribute_group');
                $modelGroup->setAttributeGroupName($set_name);
                $modelGroup->setAttributeSetId($set_id);
                
                $model->initFromSkeleton($set_skeleton);
                
                $groups = $model->getGroups();
                $groups[] = $modelGroup;
                
                $model->setGroups($groups);
                
                
                $model->save();
                
                $group_id = $modelGroup->getId();
                
                $values = array("is_required" => 1);
                $set = array("SetID" => $set_id, "GroupID" => $group_id);

                $this->createAttribute("skinned_front", "skinned_front", $values, -1, $set);
                $this->createAttribute("skinned_back", "skinned_back", $values, -1, $set);
                $this->createAttribute("skinned_left", "skinned_left", $values, -1, $set);
                $this->createAttribute("skinned_right", "skinned_right", $values, -1, $set);
                
            }
        }
    }
    
    function createAttribute($labelText, $attributeCode, $values = -1, $productTypes = -1, $setInfo = -1)
    {
        $labelText = trim($labelText);
        $attributeCode = trim($attributeCode);
 
        if($labelText == '' || $attributeCode == '')
        {
            echo ("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
            return false;
        }
 
        if($values === -1)
            $values = array();
 
        if($productTypes === -1)
            $productTypes = array();
 
        if($setInfo !== -1 && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false))
        {
            $this->logError("Please provide both the set-ID and the group-ID of the attribute-set if you'd like to subscribe to one.");
            return false;
        }
 
        echo ("Creating attribute [$labelText] with code [$attributeCode].");
 
        //>>>> Build the data structure that will define the attribute. See
        //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().
 
        $data = array(
                        'is_global'                     => '0',
                        'frontend_input'                => 'blfa_file',
                        'default_value_text'            => '',
                        'default_value_yesno'           => '0',
                        'default_value_date'            => '',
                        'default_value_textarea'        => '',
                        'is_unique'                     => '0',
                        'is_required'                   => '0',
                        'frontend_class'                => '',
                        'is_searchable'                 => '1',
                        'is_visible_in_advanced_search' => '1',
                        'is_comparable'                 => '1',
                        'is_used_for_promo_rules'       => '0',
                        'is_html_allowed_on_front'      => '1',
                        'is_visible_on_front'           => '0',
                        'used_in_product_listing'       => '0',
                        'used_for_sort_by'              => '0',
                        'is_configurable'               => '0',
                        'is_filterable'                 => '0',
                        'is_filterable_in_search'       => '0',
                        'backend_type'                  => 'varchar',
                        'default_value'                 => '',
                    );
 
        // Now, overlay the incoming values on to the defaults.
        foreach($values as $key => $newValue)
            if(isset($data[$key]) == false)
            {
                $this->logError("Attribute feature [$key] is not valid.");
                return false;
            }
       
            else
                $data[$key] = $newValue;
 
        // Valid product types: simple, grouped, configurable, virtual, bundle, downloadable, giftcard
        $data['apply_to']       = $productTypes;
        $data['attribute_code'] = $attributeCode;
        $data['frontend_label'] = array(
                                            0 => $labelText,
                                            1 => '',
                                            3 => '',
                                            2 => '',
                                            4 => '',
                                        );
 
        //<<<<
 
        //>>>> Build the model.
 
        $model = Mage::getModel('catalog/resource_eav_attribute');
 
        $model->addData($data);
 
        if($setInfo !== -1)
        {
            $model->setAttributeSetId($setInfo['SetID']);
            $model->setAttributeGroupId($setInfo['GroupID']);
        }
 
        $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        $model->setEntityTypeId($entityTypeID);
 
        $model->setIsUserDefined(1);
 
        //<<<<
 
        // Save.
 
        try
        {
            $model->save();
        }
        catch(Exception $ex)
        {
            echo ("Attribute [$labelText] could not be saved: " . $ex->getMessage());
            return false;
        }
 
        $id = $model->getId();
 
        echo ("Attribute [$labelText] has been saved as ID ($id).");
 
        return $id;
    }
    
    public function indexAction() {
        
        Mage::dispatchEvent('before_skinner_display');
        
        $prodId = $this->getRequest()->getParam('id');
        if (!$prodId) {
            return $this->_forward('noRoute');
        }
        
        
        $model = Mage::getModel('eludo_skinner/skin');
        $collection = $model->getCollection();
        
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function setProductImage($cloneId, $path)
    {
        $product = Mage::getModel('catalog/product')->load($cloneId);
        
        
        $attributes = $product->getTypeInstance ()->getSetAttributes ();
        if (isset ( $attributes ['media_gallery'] )) {
            $gallery = $attributes ['media_gallery'];
            //Get the images
            $galleryData = $product->getMediaGallery ();
            foreach ( $galleryData ['images'] as $image ) {
                //If image exists
                if ($gallery->getBackend ()->getImage ( $product, $image ['file'] )) {
                    $gallery->getBackend ()->removeImage ( $product, $image ['file'] );
                }
            }
            $product->save ();
        }
        
        //$product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
        $product->addImageToMediaGallery($path, array ("thumbnail", "small_image", "image" ), false, false);
        Mage::getSingleton('catalog/product_action')->updateAttributes(array($product->getId()), array('thumbnail'=>$path, "small_image"=>$path, "image"=>$path), 0);
        $product->save();
    }
    
    public function cloneProduct($product_id, $imgdata)
    {
        $product = Mage::getModel('catalog/product')->load($product_id);
        
        //Clono l'oggetto
        $clone = $product->duplicate()
                ->setSku("SkinnerGenerator")
                ->setName("")
                ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        $clone->getResource()->save($clone);
        $cloneId = $clone->getId(); //prelevo il suo id
        
        //Creo l'immagine e la imposto all'oggetto
        $path = $this->createImage($cloneId, $imgdata);
        if ($path) {
            $this->setProductImage($cloneId, $path);
        }

        $clone = Mage::getModel('catalog/product')->load($cloneId);
        
        $clone->setStockData(array( 
                'is_in_stock' => 1, 
                'qty' => 1000,
                'manage_stock' => 0,
                'use_config_notify_stock_qty' => 0
            ));
        
        
        $clone->save();
        return $clone;
    }
    
    public function createImage($product_id, $encoded)
    {        
        if ($product_id && $encoded)
        {
            $encoded = str_replace(' ', '+', $encoded);
            $decoded = base64_decode($encoded);
            
            $img_path = Mage::getBaseDir('media') . DS . "skinner";
            
            if (!file_exists($img_path))
            {
                mkdir($img_path);
            }
            
            $path = $img_path. DS . "prod-".$product_id.".png";
        
            $fp = fopen($path, 'w');
            if (!$fp) return false;
            
            $fw = fwrite($fp, $decoded);
            if (!$fw) return false;
            
            fclose($fp);    
            return $path;
        }
        else
        {
            return false;
        }
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
    
    public function loadImageAction()
    {
        $loadimage = $_FILES["loadimage"];
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        $json=array();
        if ($customer_id && $loadimage)
        {
            $img_path = Mage::getBaseDir('media') . DS . "skinner";
            $fname = $loadimage['name']; //file name
            
            if (!file_exists($img_path))
            {
                mkdir($img_path);
            }
            
            $fname = "user-".$customer_id."-".$fname;
            
            try
            {  
            
                $uploader = new Varien_File_Uploader('loadimage'); //load class
                $uploader->setAllowedExtensions(array('png','jpg','gif','jpeg')); //Allowed extension for file
                $uploader->setAllowCreateFolders(true); //for creating the directory if not exists
                $uploader->setAllowRenameFiles(false); //if true, uploaded file's name will be changed, if file with the same name already exists directory.
                $uploader->setFilesDispersion(false);
                $uploader->save($img_path,$fname); //save the file on the specified path
                
                $json['success'] = 'Saved '.$fname;
            }
            catch (Exception $e)
            {
                $json['error'] = 'Error Message: '.$e->getMessage();
            }
        }
        else
        {
            $json['error'] = $loadimage;
        }
        
        echo json_encode($json);
    }
    
    public function addToCartAction()
    {
        $product_id = $this->getRequest()->getPost("product_id");
	$encoded = $this->getRequest()->getPost("imgdata");
        
        //Clono l'oggetto
	$clone = $this->cloneProduct($product_id, $encoded);
        
        $json=array();
	if ($clone)
	{
            $cart = Mage::getModel('checkout/cart');
            $cart->init();
            $cart->addProduct($clone, array('qty' => 1));
            $cart->save();

            Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

            $json['success'] = Mage::getUrl('checkout/cart');
	}
	else
	{
            $json["error"] = "Error";
	}
        echo json_encode($json);
    }
    
    public function checkLoggedAction()
    {
        $json = array();
        $json['result'] = Mage::getSingleton('customer/session')->isLoggedIn();
        
        echo json_encode($json);
    }
    
    public function getAttributesAction()
    {
        $collection = Mage::getModel("catalog/product");
        $collection->load(1);
        
        $attributes = array (
            $collection->getResource()->getAttribute("skinned_front")->getFrontendlabel() => $collection->getResource()->getAttribute("skinned_front")->getFrontend()->getValue($collection),
            $collection->getResource()->getAttribute("skinned_back")->getFrontendlabel() => $collection->getResource()->getAttribute("skinned_back")->getFrontend()->getValue($collection),
            $collection->getResource()->getAttribute("skinned_left")->getFrontendlabel() => $collection->getResource()->getAttribute("skinned_left")->getFrontend()->getValue($collection),
            $collection->getResource()->getAttribute("skinned_right")->getFrontendlabel() => $collection->getResource()->getAttribute("skinned_right")->getFrontend()->getValue($collection),
        );
        
        echo "<pre>";
        print_r($attributes);
        echo "</pre>";
    }
    
    public function loadDataAction()
    {
        $collection = Mage::getModel('eludo_skinner/save')->getCollection();
        $product_id = $this->getRequest()->getPost("product_id");
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        $json=array();
        if ($collection && $customer_id)// && $product_id)
        {
            $collection->addFieldToFilter("user_id",$customer_id);
            $collection->addFieldToFilter("product_id",$product_id);
            
            $json['success'] = json_decode($collection->getFirstItem()->getValue());
            
        }
        else
        {
            $json["error"] = "Error";
        }
        
        echo json_encode($json);
    }
    
    public function downloadImageAction()
    {
        $filename = $this->getRequest()->getPost("name");
        
        header('Content-type: image/png');
        header('Content-Disposition: attachment; filename="'.$filename.'"');

        $encoded = $this->getRequest()->getPost("imgdata");
        $encoded = str_replace(' ', '+', $encoded);
        $decoded = base64_decode($encoded);
        
        echo $decoded;
    }
    
    public function saveImageAction()
    { 
        $product_id = $this->getRequest()->getPost("name");
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        $json=array();
        if ($product_id && $customer_id)
        {
            $encoded = $this->getRequest()->getPost("imgdata");
            $encoded = str_replace(' ', '+', $encoded);
            $decoded = base64_decode($encoded);
            
            $img_path = Mage::getBaseDir('media') . DS . "skinner";
            
            if (!file_exists($img_path))
            {
                mkdir($img_path);
            }
            
            $path = $img_path. DS . "prod-".$customer_id."-".$product_id.".png";
        
            $fp = fopen($path, 'w');
            if (!$fp) $json['error'] = "Error opening file";
            
            $fw = fwrite($fp, $decoded);
            if (!$fw) $json['error'] = "Error writing file ";
            
            fclose($fp);
            
            if (!$json['error'])
                $json['success'] = "Image Saved ".$path;
            
        }
        
        echo json_encode($json);
    }
    
    public function saveDataAction()
    {
        $model = Mage::getModel('eludo_skinner/save');
        
        $data = $this->getRequest()->getPost("data");
        $product_id = $this->getRequest()->getPost("product_id");
        $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
        
        //Carico se esiste il salvataggio
        $saved;
        $collection = Mage::getModel('eludo_skinner/save')->getCollection();
        if ($collection && $customer_id)// && $product_id)
        {
            $collection->addFieldToFilter("user_id",$customer_id);
            $collection->addFieldToFilter("product_id",$product_id);
            
            $saved = $collection->getFirstItem()->getId();
        }
        
        $json=array();
        if ($model && $data && $customer_id && $product_id)
        {
            if ($saved) {
                $model->setId($saved);
            }

            $model->setValue($data);
            $model->setUserId($customer_id);
            $model->setProductId($product_id);
            
            $model->save();
            
            $json['success'] = json_encode($model->getData());
            
        }
        else
        {
            $json["error"] = "Error";
        }
        
        echo json_encode($json);
    }
    
    public function getImageAction()
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
        
        
        $res2 = array();
        foreach ($result as $image) {
            
            if (!isset($image['image_id']))
            {
                $image["image_url"] = $this->getuserImageUrl($image['image']);
            }
            else
            {
                $image["image_url"] = $this->getImageUrl($image['image']);
            }
            
            $res2[] = $image;
        }
        
        //asort($result); // Sorts the Array
        
//        echo "<pre>";
//        print_r($res2);
//        echo "</pre>";
        
        echo json_encode($res2);
    }

    public function getUserImageUrl($image_name)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."skinner/".$image_name;
        
    }
    
    public function getImageUrl($image_name)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."skins/".$image_name;
        
    }
    
    public function getTextAction()
    {
        $collection = Mage::getModel("eludo_skinner/text")->getCollection();
        echo json_encode($collection->getData());
    }
    
}
