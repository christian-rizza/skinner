<?php
/**
 * News List admin grid container
 *
 * @author E-Ludo Interactive
 */
class Eludo_Skinner_Block_Adminhtml_Skinner extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'eludo_skinner';
        $this->_controller = 'adminhtml_skinner';
        $this->_headerText = Mage::helper('eludo_skinner')->__('Manage Skins');

        parent::__construct();

        if (Mage::helper('eludo_skinner/admin')->isActionAllowed('save')) {
            $this->_updateButton('add', 'label', Mage::helper('eludo_skinner')->__('Add New Skin'));
            $this->_updateButton('add', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/skin_new') . '\')');
        } else {
            $this->_removeButton('add');
        }
        
        $this->addButton(
            'news_flush_images_cache',
            array(
                'label'      => Mage::helper('eludo_skinner')->__('Flush Images Cache'),
                'onclick'    => 'setLocation(\'' . $this->getUrl('*/*/skin_flush') . '\')',
            )
        );

    }
}