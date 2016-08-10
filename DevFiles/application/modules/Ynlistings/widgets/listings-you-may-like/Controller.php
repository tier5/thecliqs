<?php
class Ynlistings_Widget_ListingsYouMayLikeController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $table = Engine_Api::_()->getItemTable('ynlistings_listing');
        $num_of_listings = $this->_getParam('num_of_listings', 3);
        $select = $table->select()
            ->where('search = ?', 1)
            ->where('approved_status = ?', 'approved')
            ->where('status = ?', 'open')
            ->order(new Zend_Db_Expr(('rand()')))
            ->limit($num_of_listings);
        $listings = $table->fetchAll($select);
        if (count($listings) == 0) {
            $this->setNoRender(true);
        }
        $this->view->listings = $listings; 
    }
}
