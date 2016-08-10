<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 26.03.12
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */
class Page_Model_DbTable_FieldsOptions extends Engine_Db_Table
{
  protected $_name = 'page_fields_options';

  protected $_rowClass = 'Page_Model_FieldsOption';

  public function getFirstOption()
  {
    $select = $this->select()
      ->order('order')
      ->limit(1);
    return $this->fetchRow($select);
  }

  public function getOption($option_id)
  {
    $select = $this->select()
      ->where('option_id = ?', $option_id);
    return $this->fetchRow($select);
  }
}
