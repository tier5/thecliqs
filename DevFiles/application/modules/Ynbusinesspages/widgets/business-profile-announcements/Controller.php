<?php
class Ynbusinesspages_Widget_BusinessProfileAnnouncementsController extends Engine_Content_Widget_Abstract
{
  public function indexAction(){
    // Don't render this if not authorized
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }
    $this->view->business = $business = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
	
    if( !$business->isViewable() ) {
      return $this->setNoRender();
    }
	//get marked announcements of current user
    $tableMark = Engine_Api::_()->getDbtable('marks', 'ynbusinesspages');
	$ids =  array();
	$select = $tableMark->select()->where('user_id  = ?', $viewer -> getIdentity());
			
	foreach( $tableMark->fetchAll($select) as $row )
	{
		$ids[] = $row->announcement_id;
	}
	
    $table = Engine_Api::_()->getDbtable('announcements', 'ynbusinesspages');
    $select = $table->select()
      ->where('business_id = ?',$business->business_id)
      ->order('modified_date DESC');
	 if(!empty($ids))
	 {
	 	$select->where('announcement_id NOT IN (?)',$ids);
	 }
          
    $announcements = $table->fetchAll($select);
    $this->view->announcements = $announcements;
	
    if(count($announcements)<=0) {
      	return $this->setNoRender();
    }
	$menu = new Ynbusinesspages_Plugin_Menus();
	
	//TODO check allow manage
	$allow_manage = $business->isAllowed('manage_announcement');
	$this->view->allow_manage = $allow_manage;
	
    $aManageAnnouncementButton = $menu->onMenuInitialize_YnbusinesspagesManageAnnouncement();
    $this->view->aManageAnnouncementButton = $aManageAnnouncementButton;
  }
}
?>