<?php
class Mp3music_Widget_NewAlbumsRightController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	if($this->_getParam('max') != ''){       
			$this->view->limit = $this->_getParam('max');
			if ($this->view->limit <=0)
			{
				$this->view->limit = 5;
			}
		}else{
		$this->view->limit = 5; }
    $model = new Mp3music_Model_Album(array());
    $paginatorNew = $model->getAlbums($this->view->limit);
    if( count($paginatorNew) <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->paginatorNewMusic = $paginatorNew;
  }
}