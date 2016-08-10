<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Upload.php 08.09.11 11:29 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Video_Upload extends Engine_Form
{
  public function init()
  {
    // Init form
    $this
      ->setTitle('Add New Video')
			->setDescription('STORE_NEW_VIDEO_DESCRIPTION_FORM')
      ->setAttrib('id', 'form-upload')
      ->setAttrib('name', 'video_create')
      ->setAttrib('enctype','multipart/form-data');

    $user = Engine_Api::_()->user()->getViewer();

    // Init name
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

    //ADD AUTH STUFF HERE

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
    $this->addElement('Hidden', 'id', array(
      'order' => 2
    ));

    // Init submit
    $this->addElement('Button', 'upload', array(
      'label' => 'Save Video',
      'type' => 'submit',
    ));
  }

}
