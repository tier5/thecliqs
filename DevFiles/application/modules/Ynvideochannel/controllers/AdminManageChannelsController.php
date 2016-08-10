<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_AdminManageChannelsController extends Core_Controller_Action_Admin {

    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_managechannels');

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $value);
                    if ($channel)
                        $channel->delete();
                }
            }
        }

        $params = $this->_getAllParams();
        $table = Engine_Api::_()->getItemTable('ynvideochannel_channel');
        $this->view->paginator = $paginator = $table->getChannelsPaginator($params);

        $this->view->paginator->setItemCountPerPage(10);
        $page = $this->_getParam('page', 1);
        $this->view->paginator->setCurrentPageNumber($page);

        // Channel Search Form
        $this->view->form = $form = new Ynvideochannel_Form_Admin_Channel_Search();
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
                $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $id);
                if ($channel)
                    $channel -> delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            return $this->_forward('success', 'utility', 'core', array(
                'layout' => 'default-simple',
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate')->_('The channel is deleted successfully.'))
            ));
        }

        // Output
        $this->_helper->layout->setLayout('default-simple');
        $this->renderScript('admin-manage-channels/delete.tpl');
    }

    public function setFeatureAction()
    {
        $id = $this->_getParam('channel_id', null);
        if ($id) {
            $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $id);
            if ($channel) {
                $channel->is_featured = !($channel->is_featured);
                $channel->save();
                $this->view->status = 1;
                $this->view->featured = $channel->is_featured;
            } else {
                $this->view->status = 0;
            }
        } else {
            $this->view->status = 0;
        }
    }

    public function setOfDayAction()
    {
        $id = $this->_getParam('channel_id', null);
        if ($id) {
            $channel = Engine_Api::_()->getItem('ynvideochannel_channel', $id);
            if ($channel)
            {
                $channel->of_day = 1;
                $channel->save();
                $this->view->status = 1;
                $table = Engine_Api::_()->getItemTable('ynvideochannel_channel');
                $table -> update(array('of_day' => 0), "channel_id <> $id");
            } else {
                $this->view->status = 0;
            }
        } else {
            $this->view->status = 0;
        }
    }
}