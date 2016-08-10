<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
class Ynvideochannel_Widget_BrowseSearchController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $type = $this->_getParam('type', 'videos');
        $this->view->form = $form = new Ynvideochannel_Form_Search(array(
            'type' => $type
        ));

        // Get category list and nest by level
        $categories = Engine_Api::_()->getItemTable('ynvideochannel_category')->getCategories();
        unset($categories[0]);
        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $form->category->addMultiOption($category['option_id'], str_repeat("-- ", $category['level'] - 1) . $this->view->translate($category['title']));
            }
        }
        $translate = Zend_Registry::get('Zend_Translate');
        switch($type)
        {
            case "videos":
                $form->keyword->setLabel('Search video');
                $form->keyword->setAttrib('placeholder',$translate->translate('Enter video name'));
                break;
            case "channels":
                $form->keyword->setLabel('Search channel');
                $form->keyword->setAttrib('placeholder', $translate->translate('Enter channel name'));
                break;
            case "playlists":
                $form->keyword->setLabel('Search playlist');
                $form->keyword->setAttrib('placeholder', $translate->translate('Enter playlist name'));
                break;
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $action = "browse-".$type;

        $actionName = $request -> getActionName();
        if(strpos($actionName, 'manage') !== false || $actionName == 'favorites' || $actionName == 'subscriptions')
        {
            $action = $actionName;
        }
        $form->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => $action), 'ynvideochannel_general', true));

        // Populate form
        $params = $request->getParams();
        $form->populate($params);
    }
}
