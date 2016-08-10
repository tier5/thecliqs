<?php
class Ynlistings_Widget_ListingAlbumsController extends Engine_Content_Widget_Abstract
{
	protected $_childCount;
	public function indexAction()
	{
		if ($this -> _getParam('itemCountPerPage') != '' && $this -> _getParam('itemCountPerPage') >= 0)
		{
			$itemCountPerPage = $this -> _getParam('itemCountPerPage');
		}
		else
		{
			$itemCountPerPage = 6;
		}
		// Don't render this if not authorized
		$this -> view -> viewer = $viewer = Engine_Api::_() -> user() -> getViewer();
		if (!Engine_Api::_() -> core() -> hasSubject())
		{
			return $this -> setNoRender();
		}
		// Get subject and check auth
		$this -> view -> listing = $subject = Engine_Api::_() -> core() -> getSubject('ynlistings_listing');

		// Get paginator
		$params['listing_id'] = $subject -> listing_id;
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('ynlistings_album') -> getAlbumsPaginator($params);

		// Set item count per page and current page number
		$paginator -> setItemCountPerPage($this -> _getParam('itemCountPerPage', $itemCountPerPage));
		$paginator -> setCurrentPageNumber($this -> _getParam('page', 1));

		// Add count to title if configured
		if ($this -> _getParam('titleCount', false) && $paginator -> getTotalItemCount() > 0)
		{
			$this -> _childCount = $paginator -> getTotalItemCount();
		}
		$this -> view -> canUpload = $subject->canUploadPhotos();
        
	}

	public function getChildCount()
	{
		return $this -> _childCount;
	}

}
?>
