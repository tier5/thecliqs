<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: ItemEdit.php minhnc $
 * @author     MinhNC
 */
class Ynmobile_Form_Admin_Menu_ItemEdit extends Ynmobile_Form_Admin_Menu_ItemCreate
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Menu Item');
    $this->submit->setLabel('Edit Menu Item');
  }
}