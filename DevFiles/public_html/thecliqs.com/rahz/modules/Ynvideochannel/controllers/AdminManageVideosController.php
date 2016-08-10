<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_AdminManageVideosController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_managevideos');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $video = Engine_Api::_()->getItem('ynvideochannel_video', $value);
                    if ($video)
                        $video->delete();
                }
            }
        }

        $params = $this->_getAllParams();
        $table = Engine_Api::_()->getItemTable('ynvideochannel_video');
        $this->view->paginator = $paginator = $table->getVideosPaginator($params);

        $this->view->paginator->setItemCountPerPage(10);
        $page = $this->_getParam('page', 1);
        $this->view->paginator->setCurrentPageNumber($page);

        // Video Search Form
        $this->view->form = $form = new Ynvideochannel_Form_Admin_Video_Search();
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
                $video = Engine_Api::_()->getItem('ynvideochannel_video', $id);
                if ($video)
                    $video -> delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The video is deleted successfully.'))
            ));
        }

        // Output
        $this->_helper->layout->setLayout('default-simple');
        $this->renderScript('admin-manage-videos/delete.tpl');
    }

    public function setFeatureAction()
    {
        $id = $this->_getParam('video_id', null);
        if ($id) {
            $video = Engine_Api::_()->getItem('ynvideochannel_video', $id);
            if ($video) {
                $video->is_featured = !($video->is_featured);
                $video->save();
                $this->view->status = 1;
                $this->view->featured = $video->is_featured;
            } else {
                $this->view->status = 0;
            }
        } else {
            $this->view->status = 0;
        }
    }
}