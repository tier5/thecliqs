<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Model_DbTable_Ruleoptions extends Engine_Db_Table
{
	public function getOptionsForRule($ruleId)
	{		
		$select = $this->select()->where('rule_id = ?', $ruleId)->order('ordering');
		$options = array();
		foreach ($this->fetchAll($select) as $ruleOption)
		{
			$options[$ruleOption->option_value] = $ruleOption->option_label;
		}
		return $options;
	}
}
