<?php
class Ynsocialads_Model_Adblock extends Core_Model_Item_Abstract {
    public function getPageName() 
    {
         $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
         $pageSelect = $pageTable->select()->where('page_id = ?', $this->page_id);
         $page = $pageTable->fetchRow($pageSelect);
         if ($page)
		 {
         	return $page -> displayname;
		 }
		 elseif(Engine_Api::_() -> hasModuleBootstrap('ynsocialadspage'))
		 {
		 	$proxyTable = Engine_Api::_()->getDbtable('proxies', 'ynsocialadspage');
			$pageSelect = $proxyTable->select()->where('page_id = ?', $this -> page_id);
         	$page = $proxyTable->fetchRow($pageSelect);
			if ($page)
			{
			 	return $page -> title;
			}
			else 
			{
				 return "";
			}
		 }
		 else 
		 {
			 return "";
		 }
    }
    
    public function deleteContent() {
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $content = $contentTable->fetchRow($contentTable->select()->where('content_id = ?', $this->content_id));
        if ($content) {
            $content->delete();
        }
    }
    
    public function updateContent() {
        $contentTable = Engine_Api::_()->getDbtable('content', 'core');
        $content = $contentTable->fetchRow($contentTable->select()->where('content_id = ?', $this->content_id));
        if ($content){
            $content->params = array(
                'adblock_id' => $this->adblock_id,
                'ads_limit' => $this->ads_limit,
                'ajax' => $this->ajax,
                'enable' => $this->enable,
            );
            $content->save();
        }
    }
}