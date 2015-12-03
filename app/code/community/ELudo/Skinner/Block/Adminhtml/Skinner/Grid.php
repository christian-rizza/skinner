<?php
/**
 * News List admin grid
 *
 * @author E-Ludo Interactive
 */
class ELudo_Skinner_Block_Adminhtml_Skinner_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init Grid default properties
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('skinner_list_grid');
        $this->setDefaultSort('skin_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('eludo_skinner/skin')->getResourceCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Catalog_Search_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('skin_id', array(
            'header'    => Mage::helper('eludo_skinner')->__('ID'),
            'width'     => '50px',
            'index'     => 'skin_id',
        ));
        
        $this->addColumn('image', array(
            'header'    => Mage::helper('eludo_skinner')->__('Image'),
            'index'     => 'image',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('eludo_skinner')->__('News Title'),
            'index'     => 'title',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('eludo_skinner')->__('Action'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(array(
                    'caption' => Mage::helper('eludo_skinner')->__('Edit'),
                    'url'     => array('base' => '*/*/skin_edit'),
                    'field'   => 'id'
                )),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'news',
        ));

        return parent::_prepareColumns();
    }
    
    /**
     * Return row URL for js event handlers
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/skin_edit', array('id' => $row->getId()));
    }

    /**
     * Grid url getter
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/skin_grid', array('_current' => true));
    }
}