<?php
class Ynlistings_Model_Value extends Fields_Model_Abstract
{
  public function getValue()
  {
    return $this->value;
  }

  public function formatValue()
  {
    return $this->value; //.' (formatted)';
  }
}