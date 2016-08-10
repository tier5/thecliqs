<?php
class Ynlistings_Widget_ListingsTagsController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
        $tag_table = Engine_Api::_()->getDbtable('tags', 'core');
        $tag_map_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
        $listing_table = Engine_Api::_()->getItemTable('ynlistings_listing');
        $tag_name = $tag_table->info('name');
        $tag_map_name = $tag_map_table->info('name');
        $listing_name = $listing_table->info('name');
    
        $filter_select = $tag_map_table->select()->from($tag_map_name,"$tag_map_name.*")
            ->setIntegrityCheck(false)
            ->joinLeft($listing_name,"$listing_name.listing_id = $tag_map_name.resource_id",'')
            ->where("$listing_name.search = ?","1")
            ->where("$listing_name.status = ?","open")
            ->where("$listing_name.approved_status = ?","approved");
    
        $select = $tag_table->select()->from($tag_name,array("$tag_name.*","Count($tag_name.tag_id) as count"));
        $select  ->joinLeft($filter_select, "t.tag_id = $tag_name.tag_id",'');
        $select  ->order("$tag_name.text");
        $select  ->group("$tag_name.text");
        $select  ->where("t.resource_type = ?","ynlistings_listing");
    
        if(Engine_Api::_()->core()->hasSubject('user')){
          $user = Engine_Api::_()->core()->getSubject('user');
          $select -> where("t.tagger_id = ?", $user->getIdentity());
        }
        else if( Engine_Api::_()->core()->hasSubject('ynlistings_listing') ) {
          $listing = Engine_Api::_()->core()->getSubject('ynlistings_listing');
          $user = $listing->getOwner();
          $select -> where("t.tagger_id = ?", $user->getIdentity());
        }
        if ($user) {
            $this->view->user = $user->getIdentity();
        }
    	$tags = $tag_table->fetchAll($select);
		if(count($tags) < 1)
		{
			return $this->setNoRender();
		}
        $this->view->tags = $tags;
    }
}
?>
