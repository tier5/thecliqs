<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_PlaylistController extends Core_Controller_Action_Standard
{
    protected $_roles;

    public function init()
    {
        // Must be able to use playlists
        if (!$this->_helper->requireAuth()->setAuthParams('ynvideochannel_playlist', null, 'view')->isValid()) {
            return;
        }

        // Get subject
        $playlist = null;
        $id = $this->_getParam('playlist_id', $this->_getParam('id', null));
        if ($id) {
            $playlist = Engine_Api::_()->getItem('ynvideochannel_playlist', $id);
            if ($playlist) {
                Engine_Api::_()->core()->setSubject($playlist);
            }
        }

        // Require subject
        if (!$this->_helper->requireSubject()->isValid()) {
            return;
        }

        // Require auth
        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'view')->isValid()) {
            return;
        }

        $this->_roles = array(
            'owner',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
        );
    }

    public function detailAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();

        $this->view->playlist = $playlist = Engine_Api::_()->core()->getSubject();

        // update view count
        if (!$playlist -> isOwner($viewer))
        {
            $playlist -> view_count++;
            $playlist -> save();
        }

        // Render
        $this->_helper->content->setEnabled();
    }

    public function editAction()
    {
        $playlist = Engine_Api::_()->core()->getSubject();

        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'edit')->isValid()) {
            return $this->_forward('requireauth', 'error', 'core');
        }

        $this -> view -> form = $form = new Ynvideochannel_Form_Playlist_Edit( array('playlist' => $playlist));

        $categories = Engine_Api::_() -> getDbTable('categories', 'ynvideochannel') -> getCategories(0);
        $categoryElement = $form -> getElement('category_id');
        foreach ($categories as $item)
        {
            $categoryElement -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $item->getTitle());
        }

        if (count($form -> category_id -> getMultiOptions()) < 1)
        {
            $form -> removeElement('category_id');
        }

        $form -> populate($playlist->toArray());

        // Render
        $this->_helper->content->setEnabled();

        // Check post/form
        if (!$this -> getRequest() -> isPost())
        {
            return;
        }
        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }

        // Process
        $db = $playlist->getTable()->getAdapter();
        $db -> beginTransaction();

        try
        {
            $values = $form -> getValues();
            $post = $this -> getRequest() -> getPost();

            $playlist -> setFromArray($values);
            $playlist -> modified_date = date('Y-m-d H:i:s');

            if (!empty($values['photo']))
            {
                $playlist -> setPhoto($form -> photo);
            }

            // Auth
            if (empty($values['auth_view']))
            {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment']))
            {
                $values['auth_comment'] = 'everyone';
            }

            $viewMax = array_search($values['auth_view'], $this -> _roles);
            $commentMax = array_search($values['auth_comment'], $this -> _roles);
            $auth = Engine_Api::_() -> authorization() -> context;
            foreach ($this->_roles as $i => $role)
            {
                $auth -> setAllowed($playlist, $role, 'view', ($i <= $viewMax));
                $auth -> setAllowed($playlist, $role, 'comment', ($i <= $commentMax));
            }

            // Rebuild privacy
            $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($playlist) as $action)
            {
                $actionTable -> resetActivityBindings($action);
            }

            // update videos order in playlist
            if (!empty($post['order'])) {
                $order = explode(',', $post['order']);
                $playlist->updateVideosOrder($order);
            }

            // remove video from playlist
            if (!empty($post['deleted'])) {
                $deleted = explode(',', $post['deleted']);
                $playlist->video_count -= count($deleted);
                $playlist->deleteVideos($deleted);
            }

            $playlist -> save();

            $db -> commit();
        }

        catch (Exception $e)
        {
            $db -> rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array(
            'action' => 'manage-playlists',
        ), 'ynvideochannel_general', true);
    }

    public function deleteAction()
    {
        $playlist = Engine_Api::_()->core()->getSubject();

        if (!$this->_helper->requireAuth()->setAuthParams($playlist, null, 'delete')->isValid()) {
            return;
        }

        $this->view->form = $form = new Ynvideochannel_Form_Playlist_Delete();

        if( !$this->getRequest()->isPost() ) {
            return;
        }
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }

        $db = $playlist->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $playlist->delete();
            $db->commit();
        } catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Playlist has been deleted.');
        return $this->_forward('success', 'utility', 'core', array(
            'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage-playlists'), 'ynvideochannel_general', true),
            'messages' => Array($this->view->message)
        ));
    }
}
