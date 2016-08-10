<?php
class Ynjobposting_Widget_HotCompaniesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$applyTbl = Engine_Api::_()->getDbTable('jobapplies', 'ynjobposting');
		$limit = $this->_getParam('itemCountPerPage', 8);
		if(!is_numeric($limit) || $limit <=0) $limit = 8;
		$this -> view -> companies = $companies = $applyTbl -> getHotCompany($limit);
		if (count($companies) == 0)
		{
			return $this->setNoRender();
		}
	}
}
