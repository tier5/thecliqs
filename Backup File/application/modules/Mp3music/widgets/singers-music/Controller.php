<?php
class Mp3music_Widget_SingersMusicController extends Engine_Content_Widget_Abstract
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
   }
}