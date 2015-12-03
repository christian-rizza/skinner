<?php
/**
 * News installation script
 *
 * @author Magento
 */

/**
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer = $this;

/**
 * Creating table eludo_skinner
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eludo_skinner/skin'))
    ->addColumn('skin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'News image media path')
    ->setComment('Skins item');

$installer->getConnection()->createTable($table);

/**
 * Creating table eludo_skinner/image
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eludo_skinner/image'))
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => true,
        'default'  => null,
    ), 'News image media path')
    ->setComment('Images item');
$installer->getConnection()->createTable($table);

/**
 * Creating table eludo_skinner/text
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eludo_skinner/text'))
    ->addColumn('text_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
    ->addColumn('text', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable' => true,
    ), 'Text')
    ->setComment('Text item');
$installer->getConnection()->createTable($table);
$installer->endSetup();

/**
 * Creating table eludo_skinner/save
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('eludo_skinner/save'))
    ->addColumn('save_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'identity' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Entity id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable' => false,
    ), 'UserId')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
    'nullable' => false,
    ), 'ProductId')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Value')
    ->setComment('Saved item');
$installer->getConnection()->createTable($table);


//=============================================================

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

        createAttribute("skinned_front", "skinned_front", $values, -1, $set);
        createAttribute("skinned_back", "skinned_back", $values, -1, $set);
        createAttribute("skinned_left", "skinned_left", $values, -1, $set);
        createAttribute("skinned_right", "skinned_right", $values, -1, $set);

    }
}

function createAttribute($labelText, $attributeCode, $values = -1, $productTypes = -1, $setInfo = -1) {
    $labelText = trim($labelText);
    $attributeCode = trim($attributeCode);

    if ($labelText == '' || $attributeCode == '') {
        echo ("Can't import the attribute with an empty label or code.  LABEL= [$labelText]  CODE= [$attributeCode]");
        return false;
    }

    if ($values === -1)
        $values = array();

    if ($productTypes === -1)
        $productTypes = array();

    if ($setInfo !== -1 && (isset($setInfo['SetID']) == false || isset($setInfo['GroupID']) == false)) {
        return false;
    }

    //echo ("Creating attribute [$labelText] with code [$attributeCode].");
    //>>>> Build the data structure that will define the attribute. See
    //     Mage_Adminhtml_Catalog_Product_AttributeController::saveAction().

    $data = array(
        'is_global' => '0',
        'frontend_input' => 'blfa_file',
        'default_value_text' => '',
        'default_value_yesno' => '0',
        'default_value_date' => '',
        'default_value_textarea' => '',
        'is_unique' => '0',
        'is_required' => '0',
        'frontend_class' => '',
        'is_searchable' => '1',
        'is_visible_in_advanced_search' => '1',
        'is_comparable' => '1',
        'is_used_for_promo_rules' => '0',
        'is_html_allowed_on_front' => '1',
        'is_visible_on_front' => '0',
        'used_in_product_listing' => '0',
        'used_for_sort_by' => '0',
        'is_configurable' => '0',
        'is_filterable' => '0',
        'is_filterable_in_search' => '0',
        'backend_type' => 'varchar',
        'default_value' => '',
    );

    // Now, overlay the incoming values on to the defaults.
    foreach ($values as $key => $newValue)
        if (isset($data[$key]) == false) {
            return false;
        } else
            $data[$key] = $newValue;

    // Valid product types: simple, grouped, configurable, virtual, bundle, downloadable, giftcard
    $data['apply_to'] = $productTypes;
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

    if ($setInfo !== -1) {
        $model->setAttributeSetId($setInfo['SetID']);
        $model->setAttributeGroupId($setInfo['GroupID']);
    }

    $entityTypeID = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
    $model->setEntityTypeId($entityTypeID);

    $model->setIsUserDefined(1);

    //<<<<
    // Save.

    try {
        $model->save();
    } catch (Exception $ex) {
        echo ("Attribute [$labelText] could not be saved: " . $ex->getMessage());
        return false;
    }

    $id = $model->getId();

    echo ("Attribute [$labelText] has been saved as ID ($id).");

    return $id;
}

//=============================================================

$installer->endSetup();
