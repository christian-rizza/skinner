<?php
/**
 * News List admin grid container
 *
 * @author E-Ludo Interactive
 */
class Eludo_Skinner_Block_Adminhtml_Image extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'eludo_skinner';
        $this->_controller = 'adminhtml_image';
        $this->_headerText = Mage::helper('eludo_skinner')->__('Manage Images');

        parent::__construct();

        if (Mage::helper('eludo_skinner/admin')->isActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('eludo_skinner')->__('Add New Images'));
            $this->_updateButton('add', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/image_new') . '\')');
        } else {
            $this->_removeButton('add');
        }

    }
}