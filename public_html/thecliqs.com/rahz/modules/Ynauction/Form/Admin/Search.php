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
class Ynauction_Form_Admin_Search extends Engine_Form {

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
    $title   ->setLabel('Title')
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
    $this->addElement('Select', 'featured', array(
      'label' => 'Featured',
      'multiOptions' => array(
        ' ' => 'All',
        '1' => 'Featured',
        '0' => 'Unfeatured',
      )
    ));
     $this->addElement('Select', 'stop', array(
      'label' => 'Stopped',
      'multiOptions' => array(
        ' ' => 'All',
        '1' => 'Stopped',
        '0' => 'Not stopped',
      )
    ));
     $this->addElement('Select', 'online', array(
      'label' => 'Published',
      'multiOptions' => array(
        ' ' => 'All',
        '1' => 'Published',
        '0' => 'Not published',
      )
    ));
     $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        ' ' => 'All',
        '0' => 'Created',
        '1' => 'Pending',
        '2' => 'Upcoming',
        '3' => 'Running',  
        '4' => 'Won',  
        '5' => 'Paid',  
        '6' => 'Ended',  
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
