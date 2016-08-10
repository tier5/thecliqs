<?php
class Mp3music_Widget_FeaturedAlbumsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$model = new Mp3music_Model_Album( array());
		$this -> view -> albums = $albums = $model -> getFeaturedAlbums();
		if (count($albums) <= 0)
			$this -> setNoRender();
		if ($this -> _getParam('max') != '')
		{
			$this -> view -> limit = $this -> _getParam('max');
			if ($this -> view -> limit <= 0)
			{
				$this -> view -> limit = 5;
			}
		}
		else
		{
			$this -> view -> limit = 5;
		}
	}

}
