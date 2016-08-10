<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 17.03.12
 * Time: 12:06
 * To change this template use File | Settings | File Templates.
 */
class Page_Form_Admin_EditTerm extends Page_Form_Admin_CreateTerm
{
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Terms')
      ->setDescription('Edit the terms, then click "Save Changes" to save your changes.');
    $this->submit->setLabel('Save Changes');
  }
}
