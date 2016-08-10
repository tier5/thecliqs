<?php
class Yncontest_Widget_ProfileInformationController extends Engine_Content_Widget_Abstract
{  
  public function indexAction()
  {

  	$this->getElement()->removeDecorator('Title');  
  	 $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }	
    // Get subject and check auth
    $contest = Engine_Api::_()->core()->getSubject();  
  
    $this->view->arrPlugins =Engine_Api::_()->yncontest()->arrPlugins;	
  	$this->view->contest = $contest;
  	$this->view->viewer = $viewer;  
  	
  	//get Tags
  	$t_table = Engine_Api::_()->getDbtable('tags', 'core');
  	$tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
  	$p_table = Engine_Api::_()->getItemTable('contest');
  	$tName = $t_table->info('name');
  	$tmName = $tm_table->info('name');
  	$pName = $p_table->info('name');
  	 
  	$filter_select = $tm_table->select()->from($tmName,"$tmName.*")
  	->setIntegrityCheck(false)
  	->joinLeft($pName,"$pName.contest_id = $tmName.resource_id",'')
  
  	->where("$pName.contest_id = ?",$contest->getIdentity());
  	
  	$select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
  	$select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
  	$select  ->order("$tName.text");
  	$select  ->group("$tName.text");
  	$select  ->where("t.resource_type = ?","contest");
  	
  	$this->view->tags = $contest->tags()->getTagMaps();
	
	//check maxentries
	$checkMaxEntries = Engine_Api::_()->yncontest()->checkMaxEntries(array(
				'contestId'=>$contest->contest_id,
				'user_id' => $contest->user_id,
		));
	//check Plugin
	switch ($contest->contest_type) {
		case 'ynblog':
			$plugin = Engine_Api::_()->yncontest()->getPluginsBlog();
			break;
		case 'advalbum':
			$plugin = Engine_Api::_()->yncontest()->getPluginsAlbum();
			break;
		case 'ynvideo':
			$plugin = Engine_Api::_()->yncontest()->getPluginsVideo();
			break;
		case 'mp3music':
			$plugin = Engine_Api::_()->yncontest()->getPluginsMusic();
			break;
	}
		
	if( $checkMaxEntries 
	&& $contest->authorization()->isAllowed($viewer,'createentries') 
	&& ($contest->start_date_submit_entries <= date('Y-m-d H:i:s') && date('Y-m-d H:i:s') <= $contest->end_date_submit_entries ) 
	&& !empty($plugin))
	{
		$this->view->maxEntries = $checkMaxEntries;
	}			
  }
}