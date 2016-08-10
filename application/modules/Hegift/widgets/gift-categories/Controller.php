<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Controller.php 08.03.12 12:30 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Widget_GiftCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->categories = $categories = Engine_Api::_()->hegift()->getCategories();

    if (!count($categories)){
      return $this->setNoRender();
    }
  }
}
