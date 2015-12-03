<?php
/**
 * News List admin grid container
 *
 * @author E-Ludo Interactive
 */
class Eludo_Skinner_Block_Adminhtml_Text extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'eludo_skinner';
        $this->_controller = 'adminhtml_text';
        $this->_headerText = Mage::helper('eludo_skinner')->__('Manage Texts');

        parent::__construct();

        if (Mage::helper('eludo_skinner/admin')->isActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('eludo_skinner')->__('Add New Text'));
            $this->_updateButton('add', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/text_new') . '\')');
        } else {
            $this->_removeButton('add');
        }

    }
}