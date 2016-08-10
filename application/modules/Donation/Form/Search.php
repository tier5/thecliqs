<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Search
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       31.07.12
 * @time       11:47
 */
class Donation_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box',
    ))
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));

    parent::init();

    $this->addElement('Text', 'search', array(
      'label' => 'DONATION_Search Donations:'
    ));

    $this->addElement('Select', 'sort', array(
      'label' => 'DONATION_Browse By:',
      'multiOptions' => array(
        'recent' => 'DONATION_Most Recent',
        'popular' => 'DONATION_Most Visited',
      ),
    ));

    // prepare categories
    $categories[0]= Zend_Registry::get('Zend_Translate')->_('DONATION_All Categories');
    $categories = array_merge($categories,Engine_Api::_()->getDbtable('categories', 'donation')->getCategoriesAssoc());
    if( count($categories) > 0 ) {
      $this->addElement('Select', 'category_id', array(
        'label' => 'Category',
        'multiOptions' => $categories,
      ));
    }
  }
}
