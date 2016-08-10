<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-11-04 17:00:11 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Page_Widget_PageCategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $set = Engine_Api::_()->page()->getCategoriesWithPages(true);

    if(!empty($set[1]))
    {
      $set = array_merge(array_slice($set, 1, count($set)), array($set[1]));
    }

    $this->view->set = $set;

    $this->view->isMultiMode = count($set) > 1;

    if (!count($set)){
      return $this->setNoRender();
		}
  }
}