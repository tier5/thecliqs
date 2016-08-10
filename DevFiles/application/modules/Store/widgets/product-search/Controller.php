<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 07.07.11 13:25 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Widget_ProductSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $values = array(
      'search' => $request->getParam('search'),
      'min_price' => $request->getParam('min_price', $translate->_('STORE_min')),
      'max_price' => $request->getParam('max_price', $translate->_('STORE_max'))
    );

    $view = $this->view;
    $view->filterForm = $filterForm = new Store_Form_Product_Search();

    $filterForm->populate($values);

    $view->topLevelId = $filterForm->getTopLevelId();
    $view->topLevelValue = $filterForm->getTopLevelValue();
  }
}