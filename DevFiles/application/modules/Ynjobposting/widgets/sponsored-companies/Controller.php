<?php
class Ynjobposting_Widget_SponsoredCompaniesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$sponsorTbl = Engine_Api::_()->getItemTable('ynjobposting_sponsor');
		$sponsorTblName = $sponsorTbl -> info('name');

		$companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
		$companyTblName = $companyTbl -> info('name');
		$limit = $this->_getParam('itemCountPerPage', 8);
		if(!is_numeric($limit) || $limit <=0) $limit = 8;
		$select = $companyTbl -> select() -> setIntegrityCheck(false)
		-> from($companyTblName)
		-> joinRight($sponsorTblName, "{$companyTblName}.company_id = {$sponsorTblName}.company_id")
		-> where("{$sponsorTblName}.active = ?", '1')
		-> where("{$companyTblName}.status = ?", 'published')
		-> limit($limit)
		;
		$this -> view -> companies = $companies = $companyTbl -> fetchAll($select);
		if (count($companies) == 0)
		{
			return $this->setNoRender();
		}
	}
}
