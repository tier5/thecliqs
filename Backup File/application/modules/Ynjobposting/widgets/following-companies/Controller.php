<?php
class Ynjobposting_Widget_FollowingCompaniesController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		if( !Engine_Api::_()->core()->hasSubject('user') ) 
		{
			return $this->setNoRender();
		} 
		$user = Engine_Api::_()->core()->getSubject('user');
		if (!$user -> getIdentity())
		{
			return $this->setNoRender();
		}
		
		$followTbl = Engine_Api::_()->getItemTable('ynjobposting_follow');
		$followTblName = $followTbl -> info('name');

		$companyTbl = Engine_Api::_()->getItemTable('ynjobposting_company');
		$companyTblName = $companyTbl -> info('name');
		
		
		$select = $companyTbl -> select() -> setIntegrityCheck(false)
		-> from($companyTblName)
		-> joinRight($followTblName, "{$companyTblName}.company_id = {$followTblName}.company_id")
		-> where("{$followTblName}.active = ?", '1')
		-> where("{$followTblName}.user_id = ?", $user->getIdentity())
		;
		$this -> view -> companies = $companies = $companyTbl -> fetchAll($select);
		if (count($companies) == 0)
		{
			return $this -> setNoRender();
		}
		
		// Add count to title if configured
	    if(count($companies) > 0 ) {	    	 
	       $this->_childCount = count($companies);	
	    }
		
	}
	public function getChildCount() {
        return $this->_childCount;
    }
}
