<?php
class Ynlistings_Widget_RelatedListingsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    if( !Engine_Api::_()->core()->hasSubject('ynlistings_listing')) {
            return $this->setNoRender();
        }
        $num_of_listings = $this->_getParam('num_of_listings', 6);
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject('ynlistings_listing');
        
        if(!$subject->authorization()->isAllowed($viewer , 'view')){
            return $this->setNoRender();
        } 
        $listing = $subject;
        $category = Engine_Api::_()->getItem('ynlistings_category', $listing->category_id);
        if ($category) {
            $table = Engine_Api::_()->getItemTable('ynlistings_listing');
            $select = $table->select()
                ->where('listing_id <> ?', $listing->getIdentity())
                ->where('category_id = ?', $category->getIdentity())
                ->where('approved_status = ?', 'approved')
                ->where('status = ?', 'open')
                ->order(new Zend_Db_Expr(('rand()')))
                ->limit($num_of_listings);
            $listingsSameCategory = $table->fetchAll($select);
        }
        else {
            $listingsSameCategory = array();
        }
        if (count($listingsSameCategory) == 0) {
            $this->setNoRender(true);
        }
        $this->view->listings = $listingsSameCategory;
    }
}
