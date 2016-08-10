<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2011-09-07 17:22:12 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Video_Edit extends Engine_Form
{
  protected $_isArray = true;

  public function init()
  {
    $this
      ->setTitle('STORE_Edit Video')
			->setDescription('STORE_NEW_VIDEO_DESCRIPTION_FORM')
      ->setAttrib('id', 'video_edit')
      ->setAttrib('enctype','multipart/form-data');

    $this->addElement('Text', 'title', array(
      'label' => 'Video Title',
      'maxlength' => '100',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        //new Engine_Filter_HtmlSpecialChars(),
        'StripTags',
        new Engine_Filter_Censor(),
        new Engine_Filter_StringLength(array('max' => '100')),
      )
    ));

    // Init descriptions
    $this->addElement('Textarea', 'description', array(
      'label' => 'Video Description',
      'filters' => array(
        'StripTags',
        new Engine_Filter_Censor(),
        //new Engine_Filter_HtmlSpecialChars(),
        new Engine_Filter_EnableLinks(),
      ),
    ));

    // Init video
    $this->addElement('Select', 'type', array(
      'label' => 'Video Source',
      'multiOptions' => array('0' => ' '),
      'onchange' => "updateVideoFields()",
    ));

    //YouTube, Vimeo
    $video_options = Array();
    $video_options[1] = "YouTube";
    $video_options[2] = "Vimeo";

    $this->type->addMultiOptions($video_options);

    // Init url
    $this->addElement('Text', 'url', array(
      'label' => 'Video Link (URL)',
      'description' => 'Paste the web address of the video here.',
      'maxlength' => '50'
    ));
    $this->url->getDecorator("Description")->setOption("placement", "append");

    $this->addElement('Hidden', 'code', array(
      'order' => 1
    ));
    $this->addElement('Hidden', 'product_id', array(
      'order' => 2
    ));
    $this->addElement('Hidden', 'ignore', array(
      'order' => 3
    ));

    $this->addElement('Hidden', 'video_id', array(
      'validators' => array(
        'Int',
      )
    ));

    $this->addElement('Button', 'upload', array(
      'label' => 'Save Changes',
      'type' => 'submit',
    ));
  }
}