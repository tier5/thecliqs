<?php
class Ynjobposting_Widget_CompanyProfileInfoController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject('ynjobposting_company'))
		{
			return $this->setNoRender();
		}
		$this -> view -> company = $company = Engine_Api::_()->core()->getSubject('ynjobposting_company');
		$this -> view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
		$this -> view -> fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($company);
		$this -> view -> industries = $industries = $company->getIndustries();
		$industryNames = array();
		foreach ($industries as $i) {
			$industryNames[] = $i -> title;
		}
		$this -> view -> industryNames = implode(", ", $industryNames);
		
		//additional infos
		$companyInfoTbl = Engine_Api::_() -> getDbTable('companyinfos', 'ynjobposting');
		$companyInfo = $companyInfoTbl -> getRowInfoByCompanyId($company -> getIdentity());
		$this -> view -> companyInfo = $companyInfo;
	}
}
