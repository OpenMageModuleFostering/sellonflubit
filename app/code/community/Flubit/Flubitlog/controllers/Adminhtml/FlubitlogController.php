<?php

/**
 * Class Flubitlog Flubitlog Controller 
 * 
 * @package Flubit
 * @category Flubitlog_FlubitlogController
 * @author Flubit team
 */
class Flubit_Flubitlog_Adminhtml_FlubitlogController extends Mage_Adminhtml_Controller_action {
	
	/* @Method :        initAction Autoloader
     * @Parameter :    None 
     * @return     :    None
     */
	
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('flubitlog/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }
	
	/* @Method :       indexAction
     * @Parameter :    None 
     * @return     :   None
     */
	 
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }
	
	/* @Method :       Method for particular logging details by auto Id
     * @Parameter :    string Id 
     * @return     :   ArrayString
     */
	 
    public function detailAction() {
        $id = $this->getRequest()->getParam('id');

        $model = Mage::getModel('flubitlog/flubitlog')->load($id);
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('flubitlog_data', $model);
            $this->loadLayout();
            //$this->_setActiveMenu('flubitlog/logs');

            //$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            //$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('flubitlog/adminhtml_flubitlog_detail'));
            
            /*$this->_addContent($this->getLayout()->createBlock('flubitlog/adminhtml_flubitlog_edit'))
                    ->_addLeft($this->getLayout()->createBlock('flubitlog/adminhtml_flubitlog_edit_tabs'));*/

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flubitlog')->__('Logs does not exist'));
            $this->_redirect('*/*/');
        }
    }
	
	public function newAction() {
        $this->_forward('edit');
    }
	
	/* @Method :       Method for particular logging for Save
     * @Parameter :    string Id
     * @return     :   ArrayString
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    //Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $uploader->save($path, $_FILES['filename']['name']);
                } catch (Exception $e) {
                    
                }

                //this way the name is saved in DB
                $data['filename'] = $_FILES['filename']['name'];
            }

            $model = Mage::getModel('flubitlog/flubitlog');
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('flubitlog')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('flubitlog')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
	
	/* @Method :       Method for delete logging by auto id
     * @Parameter :    string Id
     * @return     :   ArrayString
     */
	
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('flubitlog/flubitlog');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
	/**
     * Method for Delete Multiple Error logging
     * 
     * @param string
     * @return string
     */
	
    public function massDeleteAction() {
        $flubitlogIds = $this->getRequest()->getParam('flubitlog');
        if (!is_array($flubitlogIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($flubitlogIds as $flubitlogId) {
                    $flubitlog = Mage::getModel('flubitlog/flubitlog')->load($flubitlogId);
                    $flubitlog->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__(
                                'Total of %d record(s) were successfully deleted', count($flubitlogIds)
                        )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
     * Method for Status Update Multiple Error logging
     * 
     * @param string
     * @return string
     */
		 
    public function massStatusAction() {
        $flubitlogIds = $this->getRequest()->getParam('flubitlog');
        if (!is_array($flubitlogIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($flubitlogIds as $flubitlogId) {
                    $flubitlog = Mage::getSingleton('flubitlog/flubitlog')
                            ->load($flubitlogId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($flubitlogIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
	/**
    * Method for Export CSV files
    * 
    * @param None
    * @return xls
    */
	public function exportCsvAction() {
        $fileName = 'flubitlog.csv';
        $content = $this->getLayout()->createBlock('flubitlog/adminhtml_flubitlog_grid')
                ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }
	
	/**
    * Method for Export Xml files
    * 
    * @param None
    * @return stringxml
    */
    public function exportXmlAction() {
        $fileName = 'flubitlog.xml';
        $content = $this->getLayout()->createBlock('flubitlog/adminhtml_flubitlog_grid')
                ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
	
    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

}