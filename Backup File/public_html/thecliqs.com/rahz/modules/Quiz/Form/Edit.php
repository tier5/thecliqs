<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Edit.php 2010-07-02 19:47 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Quiz_Form_Edit extends Quiz_Form_Create
{
  public function init()
  {
    parent::init();
    
    $this->setTitle('Edit Quiz')
      ->setDescription('Edit quiz description');

    $this->submit->setLabel('Save Changes')->setName('saved');
  }
}