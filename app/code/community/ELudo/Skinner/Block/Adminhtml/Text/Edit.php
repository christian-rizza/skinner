<?php
/**
 * News List admin edit form container
 *
 * @author E-Ludo Interactive
 */
class ELudo_Skinner_Block_Adminhtml_Text_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize edit form container
     *
     */
    public function __construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'eludo_skinner';
        $this->_controller = 'adminhtml_text';
        
        parent::__construct();
        
        if (Mage::helper('eludo_skinner/admin')->isActionAllowed('save')) {
            $this->_updateButton('save', 'label', Mage::helper('eludo_skinner')->__('Save Text Item'));
            
        } else {
            $this->_removeButton('save');
        }
        
        if (Mage::helper('eludo_skinner/admin')->isActionAllowed('delete')) {
            $this->_updateButton('delete', 'label', Mage::helper('eludo_skinner')->__('Delete Text Item'));
        } else {
            $this->_removeButton('delete');
        }
        
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('form_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'form_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'form_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/text_delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
    
    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        $model = Mage::helper('eludo_skinner')->getSkinItemInstance();
        
        //print_r($model);
        if ($model->getId()) {
            return Mage::helper('eludo_skinner')->__("Edit Text Item '%s'",
                 $this->escapeHtml($model->getTitle()));
        } else {
            return Mage::helper('eludo_skinner')->__('New Text Item');
        }
    }
}