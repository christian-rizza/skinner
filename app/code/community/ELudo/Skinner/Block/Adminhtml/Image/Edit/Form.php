<?php
 
class ELudo_Skinner_Block_Adminhtml_Image_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        if (Mage::registry('skinner_item'))
        {
            $data = Mage::registry('skinner_item')->getData();
        }
        else
        {
            $data = array();
        }
 
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/image_save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('image_form', array(
             'legend' =>Mage::helper('eludo_skinner')->__('Image Information')
        ));
 
        $fieldset->addField('title', 'text', array(
             'label'     => Mage::helper('eludo_skinner')->__('Image Name'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'title'
        ));
 
        $fieldset->addField('image', 'file', array(
             'label'     => Mage::helper('eludo_skinner')->__('Image Image'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'image'
        ));
 
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}