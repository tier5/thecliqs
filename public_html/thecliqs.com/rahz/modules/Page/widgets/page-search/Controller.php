<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-11-04 18:07:11 taalay $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Page_Widget_PageSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->filterForm = new Page_Form_Search();
    $this->view->setInfoJSON = json_encode($this->view->filterForm->getSetInfo());
    $this->view->isMultiMode = count($this->view->filterForm->getSetInfo()) > 1? 'true': 'false';
  }
}