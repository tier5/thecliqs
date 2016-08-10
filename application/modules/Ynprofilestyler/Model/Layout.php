<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Model_Layout extends Core_Model_Item_Abstract
{

	public function getRules()
	{
		$ruleTable = new Ynprofilestyler_Model_DbTable_Rules;
		$ruleTableName = $ruleTable->info('name');

		$layoutRuleTable = new Ynprofilestyler_Model_DbTable_Layoutrules;
		$layoutRuleTableName = $layoutRuleTable->info('name');

		$rulesSelect = $ruleTable->select()->setIntegrityCheck(false);
		$rulesSelect->join($layoutRuleTableName, "$layoutRuleTableName.rule_id = $ruleTableName.rule_id");
		$rulesSelect->where("$layoutRuleTableName.layout_id = {$this->getIdentity()}");

		return $ruleTable->fetchAll($rulesSelect);
	}

	/**
	 * Save rules
	 * @param $rules an array of rules
	 *
	 */
	public function saveRules($rules)
	{
		try
		{
			$layoutRules = new Ynprofilestyler_Model_DbTable_Layoutrules();
			$layoutRules->delete(array('layout_id = ?' => $this->getIdentity()));

			foreach ($rules as $rule)
			{
				$layoutRule = $layoutRules->createRow(array(
					'layout_id' => $this->getIdentity(),
					'rule_id'   => $rule['rule_id'],
					'value'     => $rule['value']
				));
				$layoutRule->save();
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}

		return true;
	}

	public function saveRulesFromLayout($layout)
	{
	    $rules = array();
		foreach ($layout->getRules() as $rule)
		{
			if (!empty($rule->value))
			{
				$data = array(
					'rule_id' => $rule->getIdentity(),
					'value'   => $rule->value
				);
				array_push($rules, $data);
			}			
		}		
		
		$this->saveRules($rules);
	}

	public function getThumbnail()
	{
		$thumbnail = $this->thumbnail;
		if (empty($thumbnail))
		{
			$thumbnail = self::defaultIconUrl();
		}
		if (substr($thumbnail, 0, 1) != '/') {
			$thumbnail = '/' . $thumbnail;
		}
		return $thumbnail;
	}

	public static function defaultIconUrl()
	{
		return 'application/modules/Ynprofilestyler/externals/images/default_theme.png';
	}

	protected function _delete()
	{
		parent::_delete();

		$table = new Ynprofilestyler_Model_DbTable_Layoutrules;
		$table->delete(array('layout_id = ?' => $this->getIdentity()));
	}
}
