<?php

class Ynfbpp_AdminFieldsController extends Fields_Controller_AdminAbstract
{
    
 
    protected $_fieldType = 'user';
    protected $_requireProfileType = true;

    public function indexAction()
    {
        parent::indexAction();
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynfbpp_admin_main', array(), 'ynfbpp_admin_main_fields');
        $values = $this -> getRequest() -> getPost();

        //var_dump($values); die();
        if (isset($values))
        {
            $table = Engine_Api::_() -> getDbTable("popup", "ynfbpp");
            $table -> popupSetting($values);
            $post = $this -> getRequest() -> isPost();
            if ($post){
                $this -> view -> message = "Your changes have been saved";
            }
        }
    }

}
