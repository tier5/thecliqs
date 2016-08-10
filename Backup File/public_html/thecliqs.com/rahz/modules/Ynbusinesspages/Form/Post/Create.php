<?php
class Ynbusinesspages_Form_Post_Create extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Reply') ->setAttrib('id', 'ynbusinesspages_post_create')
      ->setAction(
        Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(array('action' => 'post', 'controller' => 'topic'), 'ynbusinesspages_extended', true)
      );
    
    $this->addElement('Textarea', 'body', array(
      'label' => 'Body',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_Censor(),
      ),
    ));

    $this->addElement('Checkbox', 'watch', array(
      'label' => 'Send me notifications when other members reply to this topic.',
      'value' => '1',
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Post Reply',
      'ignore' => true,
      'type' => 'submit',
    ));

    $this->addElement('Hidden', 'topic_id', array(
      'order' => '920',
      'filters' => array(
        'Int'
      )
    ));
	
	$this->addElement('Hidden', 'business_id', array(
      'order' => '921',
      'filters' => array(
        'Int'
      )
    ));
    
    $this->addElement('Hidden', 'ref');
  }
}