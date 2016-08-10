<?php
class Mp3music_Widget_RelatedAlbumsController extends Engine_Content_Widget_Abstract
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
    $user_id = 0;
    $album = Engine_Api::_()->core()->getSubject();
    if($album->getType() == 'mp3music_album_song')
    {
       $album = Engine_Api::_()->getItem('mp3music_album', $album->album_id);    
    }
    $user_id = $album->user_id;
    $ab_table  = Engine_Api::_()->getDbTable('albums', 'mp3music');
    $ab_name   = $ab_table->info('name');   
    $select = $ab_table->select()->from($ab_name,"$ab_name.*")
                    ->order('creation_date  DESC')
                    ->where('search = 1')  
                    ->where('user_id = ?', $user_id)  
                    ->limit($this->view->limit);                
    $paginatorNew = $ab_table->fetchAll($select);
    if( count($paginatorNew) <= 0 ) {
      return $this->setNoRender();
    }
    $this->view->paginatorNewMusic = $paginatorNew;
  }
}