<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Filter.php 04.02.12 11:40 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Form_Admin_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ));

    $this->addElement('Text', 'title', array(
      'label' => 'Display Name',
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    $this->addElement('Select', 'type', array(
      'label' => 'Type',
      'multiOptions' => array(
        '0' => ' ',
        '1' => 'Photo',
        '2' => 'Audio',
        '3' => 'Video'
      ),
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    /**
     * @var $table Hegift_Model_DbTable_Categories
     */
    $table = Engine_Api::_()->getDbTable('categories', 'hegift');
    $categories = $table->getCategoriesArray(array(0 => ' '));

    $this->addElement('Select', 'category_id', array(
      'label' => 'Category',
      'multiOptions' => $categories,
      'decorators' => array(
        'ViewHelper',
        array('Label', array('tag' => null, 'placement' => 'PREPEND')),
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    $this->addElement('Button', 'search', array(
      'label' => 'Filter',
      'ignore' => true,
      'type' => 'submit',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
        array('HtmlTag2', array('tag' => 'div')),
      ),
    ));

    $this->addElement('Hidden', 'order', array(
      'order' => 10001,
    ));

    $this->addElement('Hidden', 'order_direction', array(
      'order' => 10002,
    ));

    // Set default action without URL-specified params
    $params = array();
    foreach (array_keys($this->getValues()) as $key) {
      $params[$key] = null;
    }
    $this->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble($params));
  }
}
