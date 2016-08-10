<?php
class Ynresume_Widget_FeaturedResumeController extends Engine_Content_Widget_Abstract 
{
	public function indexAction() 
	{
	    $itemCountPerPage = $this -> _getParam('itemCountPerPage', 8);
        if (!$itemCountPerPage) {
            $itemCountPerPage = 8;
        }
		$resumeTbl = Engine_Api::_() -> getItemTable('ynresume_resume');
        $params['featured'] = '1';
		//Set curent page
		$this -> view -> paginator = $paginator = $resumeTbl -> getResumesPaginator($params);
		$paginator -> setItemCountPerPage($itemCountPerPage);
		if(!count($paginator))
		{
			return $this -> setNoRender(true);
		}
	}
}
