<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_IndexController extends Store_Controller_Action_Standard
{
  public function indexAction()
  {
    // Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function productsAction()
  {
    // Render
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function storesAction()
  {
    if ( !Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('page')) return;


    // Render 
    $this->_helper->content
      ->setNoRender()
      ->setEnabled();
  }

  public function downloadAction()
  {
    if (!($id = $this->_getParam('id'))) return $this->fileNotFound();

    /**
     * Declare Variables
     *
     * @var $viewer  User_Model_User
     * @var $item    Store_Model_Orderitem
     * @var $product Store_Model_Product
     * @var $order   Store_Model_Order
     */
    $viewer = Engine_Api::_()->user()->getViewer();

    if (
      !$viewer->getIdentity() ||
      (null == ($item = Engine_Api::_()->getItem('store_orderitem', $id))) ||
      !$item->isDownloadable() ||
      (null == ($product = $item->getItem())) ||
      (null == ($order = $item->getParent())) ||
      !$order->isOwner($viewer) ||
      (null == ($storage = $product->getFile()))
    ) {
      return $this->fileNotFound();
    }


    if (!($storage instanceof Storage_Model_File))
      return $this->fileNotFound($order->getIdentity());

    // Process the file
    $file = APPLICATION_PATH . DS . $storage->storage_path;

    if (!is_file($file)) {
      return $this->fileNotFound($order->getIdentity());
    }
    else {

      try {

        // Disable view and layout rendering
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // Execute Downloading

        // Set Headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header('Content-type: ' . $storage->mime_major . '/' . $storage->mime_minor);
        header('Content-Disposition: attachment; filename="' . $storage->name . '"');
        header('Content-Description: File Transfer');
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . $storage->size);

        // Set Body
        if (Engine_Api::_()->store()->readfile_chunked($file)) {
        // Increase download count
        $item->download_count++;
        if (!$item->save()) return $this->fileNotFound($order->order_id);
        }
      } catch (Exception $e) {
        print_log($e->__toString());
      }
    }
  }

  protected function fileNotFound($order_id = 0)
  {
    $this->view->message = $this->view->translate('STORE_Sorry, we could not find requested download file.');

    $this->_forward('success', 'utility', 'core', array(
      'layout'       => 'default',
      'redirect'     => $this->view->url(array('action'   => 'transactions',
                                               'order_id' => $order_id), 'store_panel', true),
      'redirectTime' => '3000',
      'messages'     => Array($this->view->message)
    ));
  }

  public function faqAction()
  {
    $this->view->faqs = Engine_Api::_()->getDbTable('faq', 'store')->fetchAll();
  }
}
