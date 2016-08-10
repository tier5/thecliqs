<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 23.08.12
 * Time: 17:35
 * To change this template use File | Settings | File Templates.
 */
class Donation_Form_Options_Filter extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttrib('class', 'donation_filter_form inner')
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->addDecorator('HtmlTag', array('tag'   => 'div',
        'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag'   => 'div',
        'class' => 'clear'))
      ->setAttribs(array(
        'id'    => 'search_form',
        'class' => 'donation_filter_form inner',
      ));

    // Init mode
    $this->addElement('Select', 'mode', array(
      'multiOptions' => array(
        'normal' => 'All',
        'cumulative' => 'Cumulative',
        'delta' => 'Change in',
      ),
      'value' => 'normal',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    $this->addElement('Select', 'type', array(
      'multiOptions' => array(
        '0' => 'All Donations',
        'charity' => 'Charities',
        'project' => 'Projects',
        'fundraise' => 'Fundraisings'
      ),
      'value' => '0',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    // Init period
    $this->addElement('Select', 'period', array(
      'multiOptions' => array(
        //'day' => 'Today',
        Zend_Date::WEEK => 'This week',
        Zend_Date::MONTH => 'This month',
        Zend_Date::YEAR => 'This year',
      ),
      'value' => Zend_Date::MONTH,
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    // Init chunk
    $this->addElement('Select', 'chunk', array(
      'multiOptions' => array(
        Zend_Date::DAY => 'By Day',
        Zend_Date::WEEK => 'By Week',
        Zend_Date::MONTH => 'By Month',
        Zend_Date::YEAR => 'By Year',
      ),
      'value' => 'day',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));

    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Search',
      'type' => 'submit',
      'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
      'style' => 'padding:2px',
      'decorators' => array(
        'ViewHelper',
        array('HtmlTag', array('tag' => 'div')),
      ),
    ));
  }
}
