<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Admin.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Photoviewer_Form_Admin extends Engine_Form
{

  public function init()
  {
    $this->setTitle('Photo Viewer Settings');

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $this->addElement('Checkbox', 'enable', array(
      'label' => 'Enable Photo Viewer',
      'value' => $settings->getSetting('photoviewer.enable' , 1)
    ));

    $this->addElement('Text', 'slideshowtime', array(
      'label' => 'Slideshow time per a photo (sec)',
      'value' => $settings->getSetting('photoviewer.slideshowtime' , 3),
    ));

    $this->addElement('Checkbox', 'downloadable', array(
      'label' => 'Can users to download photos',
      'value' => $settings->getSetting('photoviewer.downloadable' , 1),
    ));

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit'
    ));



  }


}

