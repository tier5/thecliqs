<?php
class Ynauction_Widget_HelpNavigatorController extends Engine_Content_Widget_Abstract{
    public function indexAction(){
        // check from help pages.
        $db = Engine_Db_Table::getDefaultAdapter();
        
        $items =  $db->fetchPairs("select helppage_id, title from engine4_ynauction_helppages where `status`='show' order by ordering asc");

        $this->view->items  =  $items;
        if(Zend_Registry::isRegistered('ACTIVE_HELP_PAGE')){
            $active_menu = Zend_Registry::get('ACTIVE_HELP_PAGE');
        }
        $this->view->active = $active_menu;
    }
}
