<?php
 
class ELudo_Skinner_Block_Adminhtml_Skinner_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
                'action' => $this->getUrl('*/*/skin_save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('skin_form', array(
             'legend' =>Mage::helper('eludo_skinner')->__('Skin Information')
        ));
 
        $fieldset->addField('title', 'text', array(
             'label'     => Mage::helper('eludo_skinner')->__('Skin Name'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'title'
        ));
 
        $fieldset->addField('image', 'file', array(
             'label'     => Mage::helper('eludo_skinner')->__('Skin Image'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'image'
        ));
 
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}