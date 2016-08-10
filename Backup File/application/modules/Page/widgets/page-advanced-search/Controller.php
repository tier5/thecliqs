<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Page_Widget_PageAdvancedSearchController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->advSearch = $form= new Page_Form_AdvSearch();
  }
}