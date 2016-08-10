<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 12.04.12 18:19 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProfileWishListController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;

  public function indexAction()
  {
    /**
     * @var $select Zend_Db_Table_Select
     * @var $table Store_Model_DbTable_Products
     * @var $paginator Zend_Paginator
     */

    if( !Engine_Api::_()->core()->hasSubject() ) {
      $id = $this->_getParam('id');
      if( null !== $id ) {
        $subject = Engine_Api::_()->user()->getUser($id);
        if( $subject->getIdentity() ) {
          Engine_Api::_()->core()->setSubject($subject);
        } else {
          return $this->setNoRender();
        }
      } else {
        return $this->setNoRender();
      }
    }

    // Get subject and check auth
    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');

    /* params */
    $fc = Zend_Controller_Front::getInstance();
    $request = $fc->getRequest();
    $page = $request->getParam('page', 1);

    $table = Engine_Api::_()->getDbTable('products', 'store');
    $prefix = $table->getTablePrefix();

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'))
      ->joinLeft(array('w'=>$prefix.'store_wishes'), "w.product_id = ".$prefix."store_products.product_id")
      ->joinLeft(array('v'=>$prefix.'store_product_fields_values'), "v.item_id = ".$prefix."store_products.product_id")
      ->joinLeft(array('o'=>$prefix.'store_product_fields_options'), "o.option_id = v.value AND o.field_id = 1", array("category" => "o.label"))
      ->where('w.user_id = ?', $subject->getIdentity())
      ->group($prefix.'store_products.product_id');

    $select = $table->setStoreIntegrity($select);

    $select
      ->where($prefix.'store_products.quantity <> 0 OR ' . $prefix.'store_products.type = ?', 'digital')
      ->where('w.user_id = ?', $subject->getIdentity())
    ;

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 15));
    $paginator->setCurrentPageNumber($page);

    $count = $paginator->getTotalItemCount();

    if (!$count) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if ( $this->_getParam('titleCount', false)) {
      $this->_childCount = $count;
    }
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}
