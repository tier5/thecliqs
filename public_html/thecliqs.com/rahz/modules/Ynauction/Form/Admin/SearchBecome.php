<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Search.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_Admin_SearchBecome extends Engine_Form {

  public function init() {
    $this
            ->clearDecorators()
            ->addDecorator('FormElements')
            ->addDecorator('Form')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
            ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
    ;
     $this   ->setAttribs(array(
                'id' => 'filter_form',
                'class' => 'global_form_box',
                'method'=>'GET',
            ));
    $title = new Zend_Form_Element_Text('title');
    $title   ->setLabel('Username')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
            ->addDecorator('HtmlTag', array('tag' => 'div'))
           ;
     
    $submit = new Zend_Form_Element_Button('search', array('type' => 'submit','name'=>'minh'));
    $submit
            ->setLabel('Search')
            ->clearDecorators()
            ->addDecorator('ViewHelper')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'buttons'))
            ->addDecorator('HtmlTag2', array('tag' => 'div'));

    $this->addElements(array(
        $title
    ));
    $this->addElement('Select', 'approved', array(
      'label' => 'Approved',
      'multiOptions' => array(
        ' ' => 'All',
        '1' => 'Approved',
        '-1' => 'Denied',
        '0' => 'Not update',
      )
    ));
     // Element: order
    $this->addElement('Hidden', 'order', array(
      'order' => 10004,
    ));

    // Element: direction
    $this->addElement('Hidden', 'direction', array(
      'order' => 10005,
    ));
    $this->addElements(array(
        $submit
    ));

  }

}
