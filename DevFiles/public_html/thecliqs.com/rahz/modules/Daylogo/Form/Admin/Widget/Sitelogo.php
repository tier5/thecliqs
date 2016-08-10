<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Sitelogo.php 2012-08-16 16:33 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Daylogo_Form_Admin_Widget_Sitelogo extends Core_Form_Admin_Widget_Standard
{
  public function init()
  {

    parent::init();

    // Set form attributes
    $this
      ->setTitle('DAYLOGO_Default_Logo')
      ->setDescription('DAYLOGO_Default_Logo_Description')
    ;

    $logoParams = Engine_Api::_()->getDbTable('logos', 'daylogo')->getLogoParams('daylogo.day-logo');

    if( !array_key_exists('logo', $logoParams) ) {
      $logoParams['logo'] = "";
    }
    if( !array_key_exists('logo_id', $logoParams) or !is_numeric($logoParams['logo_id'])) {
      $logoParams['logo_id'] = 0;
    }

    $this->addElement('Hidden', 'logo', array(
      'value' => $logoParams['logo'],
      'order' => 990
    ));

    $this->addElement('Hidden', 'logo_id', array(
      'value' => (int)$logoParams['logo_id'],
      'order' => 991,
      'filters' => array(
        'Int'
      )
    ));

    // Get available files
    $logoOptions = array('' => 'Text-only (No logo)');
    $imageExtensions = array('gif', 'jpg', 'jpeg', 'png');

    $it = new DirectoryIterator(APPLICATION_PATH . '/public/admin/');
    foreach( $it as $file ) {
      if( $file->isDot() || !$file->isFile() ) continue;
      $basename = basename($file->getFilename());
      if( !($pos = strrpos($basename, '.')) ) continue;
      $ext = strtolower(ltrim(substr($basename, $pos), '.'));
      if( !in_array($ext, $imageExtensions) ) continue;
      $logoOptions['public/admin/' . $basename] = $basename;
    }
    $this->addElement('Select', 'default', array(
      'label' => 'DAYLOGO_Default_Logo',
      'description' => $this->getView()->translate('DAYLOGO_Default_Logo_Description'),
      'multiOptions' => $logoOptions,
    ));

    $this->default->addDecorator('Description');
    $this->default->getDecorator('Description')->setOption('escape', false);
    $this->default->getDecorator('Description')->setOption('placement', 'prepend');
  }
}