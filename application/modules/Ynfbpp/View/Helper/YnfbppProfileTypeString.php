<?php

class Ynfbpp_View_Helper_YnfbppProfileTypeString extends Zend_View_Helper_Abstract{

	public function ynfbppProfileTypeString($subject){
		$fieldsByAlias = Engine_Api::_() -> fields() -> getFieldsObjectsByAlias($subject);

		if (!empty($fieldsByAlias['profile_type']))
		{
		    $optionId = $fieldsByAlias['profile_type'] -> getValue($subject);
		    if ($optionId)
		    {
		        $optionObj = Engine_Api::_() -> fields() -> getFieldsOptions($subject) -> getRowMatching('option_id', $optionId -> value);
		        if ($optionObj)
		        {

		            return $optionObj -> label;
		        }
		    }
		}
		return '';
	}
}
