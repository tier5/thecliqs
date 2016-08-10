<?php
class Ynlistings_Widget_MostDiscussionListingsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
	    $num_of_listings = $this->_getParam('num_of_listings', 3);
        $listingTable = Engine_Api::_()->getItemTable('ynlistings_listing');
        $listingTblName = $listingTable->info('name');
        $postTable = Engine_Api::_()->getItemTable('ynlistings_post');
        $postTblName = $postTable->info('name');
        $select = $listingTable->select()
            ->from($listingTblName, "$listingTblName.*, COUNT($postTblName.post_id) as discuss_count")
            ->joinLeft("$postTblName","$postTblName.listing_id = $listingTblName.listing_id", "")
            ->where("$listingTblName.search = ?", 1)
            ->where("$listingTblName.status = ?", "open")
            ->where("$listingTblName.approved_status = ?", "approved")
            ->group("$listingTblName.listing_id") -> order("COUNT($postTblName.post_id) DESC")->limit($num_of_listings);
        $this->view->listings = $listings = $listingTable->fetchAll($select);
        if (count($listings) == 0) {
            $this->setNoRender(true);
        }
    }
}
