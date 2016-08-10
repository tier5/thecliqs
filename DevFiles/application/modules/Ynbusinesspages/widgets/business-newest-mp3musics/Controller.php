<?php
class Ynbusinesspages_Widget_BusinessNewestMp3MusicsController extends Engine_Content_Widget_Abstract {
	protected $_childCount;
    public function indexAction(){
   		$music_enable = Engine_Api::_() -> hasModuleBootstrap('mp3music');
		
		if (!$music_enable) {
			$this -> setNoRender();
		}
        
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }
        
		$this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
		
		if (!$business -> isViewable() || !$business -> getPackage() -> checkAvailableModule('mp3music_album')) {
            return $this -> setNoRender();
        }
        
		//Get search condition
		$params = array();
		$params['business_id'] = $business -> getIdentity();
		$params['order'] ='recent';
		$params['ItemTable'] = 'mp3music_album';
        $limit = $this -> _getParam('itemCountPerPage', 1);
        if (!$limit) {
            $limit = 1;
        }
        $params['limit'] = $limit;
        
		//Get Album paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getDbTable('mappings', 'ynbusinesspages') -> getAlbumsPaginator($params);
		if ($paginator->getTotalItemCount() <= 0) {
            $this->setNoRender();
        }
    }
}