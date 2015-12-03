<?php

class ELudo_Skinner_Adminhtml_SkinnerController extends Mage_Adminhtml_Controller_Action 
{
    
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('skinner/manage')
            ->_addBreadcrumb(
                  Mage::helper('eludo_skinner')->__('Skinner'),
                  Mage::helper('eludo_skinner')->__('Skinner')
              )
            ->_addBreadcrumb(
                  Mage::helper('eludo_skinner')->__('Manage Skin'),
                  Mage::helper('eludo_skinner')->__('Manage Skin')
              )
        ;
        return $this;
    }
    
    /********************************
     * TEXT CONTROLLER FUNCTIONS
     *******************************/
    public function textAction() {
        $this->_title($this->__('Skinner'))
             ->_title($this->__('Manage Images'));
        
        $this->_initAction();
        $this->renderLayout();
    }
    public function text_newAction() {
        // the same form is used to create and edit
        $this->_forward('text_edit');
    }
    public function text_editAction() {
        $this->_title($this->__('Text'))
             ->_title($this->__('Manage Texts'));
        
        // 1. instance text model
        $model = Mage::getModel('eludo_skinner/text');
        
        $skinID = $this->getRequest()->getParam('id');
        
        if ($skinID) {
            
            $model->load($skinID);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('eludo_skinner')->__('News item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('eludo_skinner')->__('Edit Item');            
            
        } else {
            
            $this->_title(Mage::helper('eludo_skinner')->__('New Item'));
            $breadCrumb = Mage::helper('eludo_skinner')->__('New Item');
        }
        
        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);
        
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        
        if (!empty($data)) {
            $model->addData($data);
        }
        
        // 4. Register model to use later in blocks
        Mage::register('skinner_item', $model);

        // 5. render layout
        $this->renderLayout();
    }
    public function text_saveAction() {
        
        $redirectPath   = '*/*/text';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // init model and set data
            $model = Mage::getModel('eludo_skinner/text');
            // if skin item exists, try to load it
            $skinId = $this->getRequest()->getParam('image_id');
            if ($skinId) {
                $model->load($skinId);
            }
            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            
            
            $model->addData($data);

            try {
                
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('eludo_skinner')->__('The item has been saved.')
                );
                
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('eludo_skinner')->__(print_r($e,true)." ".'An error occurred while saving the skin item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/text_edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }
    public function text_deleteAction() {
        
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        
        if ($itemId) 
        {
            try {
                // init model and delete
                $model = Mage::getModel('eludo_skinner/text');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('eludo_skinner')->__('Unable to find a news item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('eludo_skinner')->__('The item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('eludo_skinner')->__('An error occurred while deleting the news item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/text');
        
    }
    
    
    
    /********************************
     * IMAGE CONTROLLER FUNCTIONS
     *******************************/
    public function imageAction() {
        $this->_title($this->__('Skinner'))
             ->_title($this->__('Manage Images'));
        
        $this->_initAction();
        $this->renderLayout();
    }
    public function image_newAction() {
        // the same form is used to create and edit
        $this->_forward('image_edit');
    }
    public function image_editAction() {
        $this->_title($this->__('Text'))
             ->_title($this->__('Manage Texts'));
        
        // 1. instance news model
        $model = Mage::getModel('eludo_skinner/image');
        
        $skinID = $this->getRequest()->getParam('id');
        
        if ($skinID) {
            
            $model->load($skinID);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('eludo_skinner')->__('News item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('eludo_skinner')->__('Edit Item');            
            
        } else {
            
            $this->_title(Mage::helper('eludo_skinner')->__('New Item'));
            $breadCrumb = Mage::helper('eludo_skinner')->__('New Item');
        }
        
        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);
        
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        
        if (!empty($data)) {
            $model->addData($data);
        }
        
        // 4. Register model to use later in blocks
        Mage::register('skinner_item', $model);

        // 5. render layout
        $this->renderLayout();
    }
    public function image_saveAction() {
        
        $redirectPath   = '*/*/image';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // init model and set data
            $model = Mage::getModel('eludo_skinner/image');
            // if skin item exists, try to load it
            $skinId = $this->getRequest()->getParam('image_id');
            if ($skinId) {
                $model->load($skinId);
            }
            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            
            
            $model->addData($data);

            try {
                $hasError = false;
                
                $imageHelper = Mage::helper('eludo_skinner/image');
                
                // remove image
                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }

                // upload new image
                $imageFile = $imageHelper->uploadImage('image');
                
                if ($imageFile) {
                    
                    if ($model->getImage()) {
                        $imageHelper->removeImage($model->getImage());
                    }
                    $model->setImage($imageFile);
                }
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('eludo_skinner')->__('The item has been saved.')
                );
                
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('eludo_skinner')->__(print_r($e,true)." ".'An error occurred while saving the skin item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/image_edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }
    public function image_deleteAction() {
        
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        
        if ($itemId) 
        {
            try {
                // init model and delete
                $model = Mage::getModel('eludo_skinner/image');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('eludo_skinner')->__('Unable to find a news item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('eludo_skinner')->__('The item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('eludo_skinner')->__('An error occurred while deleting the news item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/image');
        
    }
    
    /********************************
     * SKIN CONTROLLER FUNCTIONS
     *******************************/
    public function skinAction() {
        $this->_title($this->__('Skinner'))
             ->_title($this->__('Manage Skins'));
        
        $this->_initAction();
        $this->renderLayout();
    }
    public function skin_deleteAction() {
        // check if we know what should be deleted
        $itemId = $this->getRequest()->getParam('id');
        
        if ($itemId) 
        {
            try {
                // init model and delete
                $model = Mage::getModel('eludo_skinner/skin');
                $model->load($itemId);
                if (!$model->getId()) {
                    Mage::throwException(Mage::helper('eludo_skinner')->__('Unable to find a news item.'));
                }
                $model->delete();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('eludo_skinner')->__('The item has been deleted.')
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('eludo_skinner')->__('An error occurred while deleting the news item.')
                );
            }
        }

        // go to grid
        $this->_redirect('*/*/skin');
    }
    public function skin_newAction() {
        // the same form is used to create and edit
        $this->_forward('skin_edit');
    }
    public function skin_saveAction() {
        $redirectPath   = '*/*/skin';
        $redirectParams = array();

        // check if data sent
        $data = $this->getRequest()->getPost();
        if ($data) {
            // init model and set data
            $model = Mage::getModel('eludo_skinner/skin');
            // if skin item exists, try to load it
            $skinId = $this->getRequest()->getParam('skin_id');
            if ($skinId) {
                $model->load($skinId);
            }
            // save image data and remove from data array
            if (isset($data['image'])) {
                $imageData = $data['image'];
                unset($data['image']);
            } else {
                $imageData = array();
            }
            
            
            $model->addData($data);

            try {
                $hasError = false;
                
                /* @var $imageHelper ELudo_Skinner_Helper_Image */
                $imageHelper = Mage::helper('eludo_skinner/image');
                
                // remove image
                if (isset($imageData['delete']) && $model->getImage()) {
                    $imageHelper->removeImage($model->getImage());
                    $model->setImage(null);
                }

                // upload new image
                $imageFile = $imageHelper->uploadImage('image');
                
                if ($imageFile) {
                    
                    if ($model->getImage()) {
                        $imageHelper->removeImage($model->getImage());
                    }
                    $model->setImage($imageFile);
                }
                // save the data
                $model->save();

                // display success message
                $this->_getSession()->addSuccess(
                    Mage::helper('eludo_skinner')->__('The item has been saved.')
                );
                
            } catch (Mage_Core_Exception $e) {
                $hasError = true;
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $hasError = true;
                $this->_getSession()->addException($e,
                    Mage::helper('eludo_skinner')->__(print_r($e,true)." ".'An error occurred while saving the skin item.')
                );
            }

            if ($hasError) {
                $this->_getSession()->setFormData($data);
                $redirectPath   = '*/*/skin_edit';
                $redirectParams = array('id' => $this->getRequest()->getParam('id'));
            }
        }

        $this->_redirect($redirectPath, $redirectParams);
    }
    public function skin_editAction() {
        $this->_title($this->__('Text'))
             ->_title($this->__('Manage Texts'));
        
        // 1. instance news model
        $model = Mage::getModel('eludo_skinner/skin');
        
        $skinID = $this->getRequest()->getParam('id');
        
        if ($skinID) {
            
            $model->load($skinID);

            if (!$model->getId()) {
                $this->_getSession()->addError(
                    Mage::helper('eludo_skinner')->__('News item does not exist.')
                );
                return $this->_redirect('*/*/');
            }
            // prepare title
            $this->_title($model->getTitle());
            $breadCrumb = Mage::helper('eludo_skinner')->__('Edit Item');            
            
        } else {
            
            $this->_title(Mage::helper('eludo_skinner')->__('New Item'));
            $breadCrumb = Mage::helper('eludo_skinner')->__('New Item');
        }
        
        // Init breadcrumbs
        $this->_initAction()->_addBreadcrumb($breadCrumb, $breadCrumb);
        
        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        
        if (!empty($data)) {
            $model->addData($data);
        }
        
        // 4. Register model to use later in blocks
        Mage::register('skinner_item', $model);

        // 5. render layout
        $this->renderLayout();
    }
    public function skin_gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * Index action
     */
    public function indexAction() {
        $this->_title($this->__('Skinner'))->_title($this->__('Manage Skins'));

        $this->loadLayout();
        $this->_setActiveMenu('skinner/manage');
        
        $this->_addBreadcrumb(Mage::helper('eludo_skinner')->__('Skinner'),Mage::helper('eludo_skinner')->__('Skinner'));
        $this->_addBreadcrumb(Mage::helper('eludo_skinner')->__('Manage Skin'),Mage::helper('eludo_skinner')->__('Manage Skin'));
        
        $this->renderLayout();
    }       
    
}
