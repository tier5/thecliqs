<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Filter.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_Form_Statistics_Filter extends Engine_Form
{
	public function init()
  {
  	$this->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ->setMethod('post');
      
    $this->setAttribs(array(
      'id' => 'filter_form',
      'class' => 'global_form_box page_stat_filter_form',
    ));
    
    $this->addElement('Select', 'period', array(
      'multiOptions' => array(
    		Zend_Date::MONTH => 'This month',
    		Zend_Date::YEAR => 'This year',
    	)
    ));
    
    $this->addElement('Hidden', 'type');
    
    $this->getElement('period')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
    
    $this->addElement('Select', 'chunk', array(
      'multiOptions' => array(
    		Zend_Date::DAY => 'By day',
    		Zend_Date::MONTH => 'By month',
    	)
    ));
    
    $this->getElement('chunk')
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null, 'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'hidden'));
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Filter',
      'type' => 'submit',
      'ignore' => true,
    	'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
      'decorators' => array('ViewHelper')
    ));
  }
}
