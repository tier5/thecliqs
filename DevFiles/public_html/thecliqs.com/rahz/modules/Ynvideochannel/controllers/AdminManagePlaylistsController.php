<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_AdminManagePlaylistsController extends Core_Controller_Action_Admin {

    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_manageplaylists');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $playlist = Engine_Api::_()->getItem('ynvideochannel_playlist', $value);
                    if ($playlist)
                        $playlist->delete();
                }
            }
        }

        $params = $this->_getAllParams();
        $table = Engine_Api::_()->getItemTable('ynvideochannel_playlist');
        $this->view->paginator = $paginator = $table->getPlaylistsPaginator($params);

        $this->view->paginator->setItemCountPerPage(10);
        $page = $this->_getParam('page', 1);
        $this->view->paginator->setCurrentPageNumber($page);

        // Playlist Search Form
        $this->view->form = $form = new Ynvideochannel_Form_Admin_Playlist_Search();
        $form->populate($params);
        $formValues = $form->getValues();
        if (isset($params['fieldOrder'])) {
            $formValues['fieldOrder'] = $params['fieldOrder'];
        }
        if (isset($params['order'])) {
            $formValues['order'] = $params['order'];
        }
        $this->view->params = $formValues;
    }

    public function deleteAction()
    {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        // Check post
        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $playlist = Engine_Api::_()->getItem('ynvideochannel_playlist', $id);
                if ($playlist)
                    $playlist -> delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The playlist is deleted successfully.'))
            ));
        }

        // Output
        $this->_helper->layout->setLayout('default-simple');
        $this->renderScript('admin-manage-playlists/delete.tpl');
    }
}