<?php
class Ynmusic_Widget_ListGenresController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $genresTbl = Engine_Api::_()->getDbTable('genres', 'ynmusic');
		$genres = $genresTbl->fetchAll($genresTbl->select()->where('isAdmin = ?', 1)->order('title ASC'));
        $this->view->genres = $genres;
        if (count($genres) == 0) {
            $this->setNoRender(true);
        }
    }
}