<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Level.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Form_Admin_Level extends Engine_Form
{
  protected $_roles = array(
    'everyone' => 'Everyone',
    'registered' => 'Registered Members',
    'likes' => 'Fans, Admins and Owner',
    'team' => 'Admins and Owner'
  );

  protected $_level;

  public function setLevel(Authorization_Model_Level $level)
  {
    $this->_level = $level;
  }

  public function init()
  {
    $this
      ->setTitle('Store Level Settings')
      ->setDescription('STORE_FORM_ADMIN_LEVEL_DESCRIPTION');

    /**
     * @var $table Authorization_Model_DbTable_Levels
     */
    $table = Engine_Api::_()->getDbtable('levels', 'authorization');
    $public = $table->getPublicLevel();
    $levels = array();

    foreach ($table->fetchAll($table->select()) as $row) {
      $levels[$row['level_id']] = $row['title'];
    }

    $this->addElement('Select', 'level_id', array(
      'label' => 'Member Level',
      'multiOptions' => $levels,
    ));

    $this->addElement('Radio', 'use', array(
      'label' => 'Allow Using Store?',
      'description' => 'STORE_FORM_ADMIN_LEVEL_STORE_USAGE_DESCRIPTION',
      'multiOptions' => array(
        0 => 'No, do not allow using Store.',
        1 => 'Yes, allow using Store.'
      ),
      'value' => 0
    ));

    if ($public->getIdentity() != $this->_level->getIdentity()) {
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Product Comment/Like?',
        'description' => 'STORE_FORM_ADMIN_LEVEL_COMMENT_DESCRIPTION',
        'multiOptions' => array(
          0 => 'No, do not allow comment/like products.',
          1 => 'Yes, allow comment/like products.'
        ),
        'value' => 1,
      ));

      $this->addElement('Radio', 'order', array(
        'label' => 'Allow Product Ordering?',
        'description' => 'STORE_FORM_ADMIN_LEVEL_ORDER_DESCRIPTION',
        'multiOptions' => array(
          0 => 'No, do not allow order products.',
          1 => 'Yes, allow order products.'
        ),
        'value' => 1,
      ));
    }

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}