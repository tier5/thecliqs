<?php
class Ynmusic_Widget_MostPlayedAlbumsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$params['browse_by'] = "most_played";	
		// Get paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynmusic_album') -> getAlbumsPaginator($params);
		$this->getElement()->removeDecorator('Title');
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$page = $request -> getParam('page', 1);
		//$this -> _getParam('itemCountPerPage', 8)
		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', 8));
		$paginator -> setCurrentPageNumber($page);

	}
}
?>
