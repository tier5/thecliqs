<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 26.03.12
 * Time: 13:50
 * To change this template use File | Settings | File Templates.
 */
class Page_Model_DbTable_Terms extends Engine_Db_Table
{
  protected $_rowClass = 'Page_Model_Term';

  public function getTerm($option_id)
  {
    $select = $this->select()
      ->where('option_id=?',$option_id)
      ->limit(1);

    return $this->fetchRow($select);
  }
  public function getTerms()
  {
    $select = $this->select()
      ->where('enabled=1');

    return $this->fetchAll($select);
  }
}
