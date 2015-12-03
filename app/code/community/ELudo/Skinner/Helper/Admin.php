<?php
/**
 * News Admin helper
 *
 * @author E-Ludo Interactive
 */
class Eludo_Skinner_Helper_Admin extends Mage_Core_Helper_Abstract
{
    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    public function isActionAllowed($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('skinner/skins/' . $action);
        
        }
}