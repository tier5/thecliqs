<?php
/**
 * Accordion Menu
 * @author     seTweaks
 */

class Accordion_Widget_AccordionMenuController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('transformer_accordion');

    $element = $this->getElement();
    $this->view->parentTitle = $element->getTitle();
    $element->setTitle();
  }
}
