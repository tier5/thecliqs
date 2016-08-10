<?php
class Yncontest_Widget_ProfileMyEntriesController extends Engine_Content_Widget_Abstract
{
protected $_childCount;
	public function indexAction()
	{
		$viewer = Engine_Api::_() -> core() -> getSubject();
		$params['user_id'] = $viewer -> getIdentity();

		$arrTemp = array();
		$arrTemp['advalbum']['height'] = (int)$this -> _getParam('heightadvalbum',160);
		$arrTemp['advalbum']['width'] = (int)$this -> _getParam('widthadvalbum',155);		
		$params['entry_type'] = 'advalbum';		
		
		$this -> view -> paginatoradvalbum = $paginator = Engine_Api::_() -> yncontest() -> getEntryPaginator($params);
		$this->view->items_per_pageadvalbum = $items_per_pageadvalbum = $paginator->getTotalItemCount();
		$this -> view -> paginatoradvalbum -> setItemCountPerPage($items_per_pageadvalbum);		
		
		$arrTemp['ynblog']['height'] = (int)$this -> _getParam('heightynblog',250);
		$arrTemp['ynblog']['width'] = (int)$this -> _getParam('widthynblog',90);
		$params['entry_type'] = 'ynblog';		
		
		$this -> view -> paginatorynblog = $paginator = Engine_Api::_() -> yncontest() -> getEntryPaginator($params);
		$this->view->items_per_pageynblog = $items_per_pageynblog = $paginator->getTotalItemCount();
		$this -> view -> paginatorynblog -> setItemCountPerPage($items_per_pageynblog);		
		
		$arrTemp['ynvideo']['height'] = (int)$this -> _getParam('heightynvideo',160);
		$arrTemp['ynvideo']['width'] = (int)$this -> _getParam('widthynvideo',155);
		$params['entry_type'] = 'ynvideo';		
		
		$this -> view -> paginatorynvideo = $paginator = Engine_Api::_() -> yncontest() -> getEntryPaginator($params);
		$this->view->items_per_pageynvideo = $items_per_pageynvideo = $paginator->getTotalItemCount();
		$this -> view -> paginatorynvideo -> setItemCountPerPage($items_per_pageynvideo);
		
		$arrTemp['mp3music']['height'] = (int)$this -> _getParam('heightmp3music',250);
		$arrTemp['mp3music']['width'] = (int)$this -> _getParam('widthmp3music',90);		
		$params['entry_type'] = 'mp3music';		
		
		$this -> view -> paginatormp3music = $paginator = Engine_Api::_() -> yncontest() -> getEntryPaginator($params);
		$this->view->items_per_pagemp3music = $items_per_pagemp3music = $paginator->getTotalItemCount();
		$this -> view -> paginatormp3music -> setItemCountPerPage($items_per_pagemp3music);
		
		$this->view->arrTemp = $arrTemp;
		$this -> view -> arrPlugins = Engine_Api::_() -> yncontest() -> getPlugins();
		
		if (($items_per_pageadvalbum + $items_per_pageynblog + $items_per_pageynvideo + $items_per_pagemp3music) <= 0) {
            return $this->setNoRender();
        } else {
            $this->_childCount = ($items_per_pageadvalbum + $items_per_pageynblog + $items_per_pageynvideo + $items_per_pagemp3music);
        }
	}
	public function getChildCount() {
        return $this->_childCount;
    }

}
