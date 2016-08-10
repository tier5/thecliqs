<?php class Ynvideochannel_AdminCategoryFieldsController extends Fields_Controller_AdminAbstract
{
    protected $_fieldType = 'ynvideochannel_video';

    protected $_requireProfileType = true;

    public function indexAction()
    {
        // Make navigation
        $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynvideochannel_admin_main', array(), 'ynvideochannel_admin_main_categories');
        $this -> view -> option_id = $option_id =  $this->_getParam('option_id');
        $tableCategory = Engine_Api::_()->getItemTable('ynvideochannel_category');
        $category = $tableCategory -> getCategoryByOptionId($option_id);
        $this -> view -> category = $category;
        parent::indexAction();
    }

    public function headingCreateAction()
    {
        parent::headingCreateAction();
        $form = $this->view->form;
        if($form){
            $display = $form->getElement('display');
            $display->setLabel('Show on video detail page?');
            $display->setOptions(array('multiOptions' => array(
                1 => 'Show on video detail page',
                0 => 'Hide on video detail page'
            )));
            $form -> removeElement('show');
        }
    }

    public function headingEditAction()
    {
        parent::headingEditAction();
        $form = $this->view->form;
        if($form){
            $display = $form->getElement('display');
            $display->setLabel('Show on video detail page?');
            $display->setOptions(array('multiOptions' => array(
                1 => 'Show on video detail page',
                0 => 'Hide on video detail page'
            )));
            $form -> removeElement('show');
        }
    }
    public function fieldCreateAction(){
        parent::fieldCreateAction();
        // remove stuff only relavent to profile questions
        $form = $this->view->form;

        if($form){
            $form -> removeElement('search');
            $form -> addElement('Hidden', 'search', array('value' => '0', 'order' => '101'));
            $display = $form->getElement('display');
            $display->setLabel('Show on video detail page?');
            $display->setOptions(array('multiOptions' => array(
                1 => 'Show on video detail page',
                0 => 'Hide on video detail page'
            )));
            $form -> removeElement('show');
        }
    }

    public function fieldEditAction(){
        parent::fieldEditAction();
        // remove stuff only relavent to profile questions
        $form = $this->view->form;

        if($form){
            $form -> removeElement('search');
            $form -> addElement('Hidden', 'search', array('value' => '0', 'order' => '101'));
            $display = $form->getElement('display');
            $display->setLabel('Show on video detail page?');
            $display->setOptions(array('multiOptions' => array(
                1 => 'Show on video detail page',
                0 => 'Hide on video detail page'
            )));
            $form -> removeElement('show');
        }
    }
}
?>
