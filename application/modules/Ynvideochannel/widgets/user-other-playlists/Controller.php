<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_UserOtherPlaylistsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject('ynvideochannel_playlist')) {
            return $this -> setNoRender();
        }
        $playlist = Engine_Api::_() -> core() -> getSubject('ynvideochannel_playlist');

        //CHECK FOR OWNER, IF THIS USER IS DELETED, DO NOT SHOW THE WIDGET
        $owner = $playlist->getOwner();
        if (!$owner->getIdentity()) {
            return $this->setNoRender();
        }

        // ADD OWNNER LINK TO THE WIDGET
        if (!$this->getElement()->getTitle()) {
            $this->getElement()->setTitle($this -> view -> translate("%s's other playlists", $owner));
        }

        $numberOfItems = $this->_getParam('itemCountPerPage', 5);
        $table = Engine_Api::_()->getDbTable('playlists', 'ynvideochannel');

        $select = $table->select()
            ->where('owner_id = ?', $playlist->owner_id)
            ->where('playlist_id <> ?', $playlist->getIdentity())
            ->where('search = 1')
            ->limit($numberOfItems)
            ->order(new Zend_Db_Expr(('rand()')));


        $this->view->paginator = $paginator = Zend_Paginator::factory($select);
        if ($paginator->getTotalItemCount() <= 0) {
            return $this->setNoRender();
        }
        $paginator->setItemCountPerPage($numberOfItems);
    }
}
