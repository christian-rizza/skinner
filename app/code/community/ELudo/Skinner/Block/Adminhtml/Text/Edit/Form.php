<?php
 
class ELudo_Skinner_Block_Adminhtml_Text_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
                'action' => $this->getUrl('*/*/text_save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
        ));
 
        $form->setUseContainer(true);
 
        $this->setForm($form);
 
        $fieldset = $form->addFieldset('text_form', array(
             'legend' =>Mage::helper('eludo_skinner')->__('Image Information')
        ));
 
        $fieldset->addField('text', 'text', array(
             'label'     => Mage::helper('eludo_skinner')->__('Text'),
             'class'     => 'required-entry',
             'required'  => true,
             'name'      => 'text'
        ));
 
        $form->setValues($data);
 
        return parent::_prepareForm();
    }
}