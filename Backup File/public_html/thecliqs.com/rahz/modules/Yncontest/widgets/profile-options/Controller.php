<?php
class Yncontest_Widget_ProfileOptionsController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{

		$this->getElement()->removeDecorator('Title');
		$viewer = Engine_Api::_()->user()->getViewer();

		if( !Engine_Api::_()->core()->hasSubject() ) {
			return $this->setNoRender();
		}
		
		// Get subject and check auth
		$item = Engine_Api::_()->core()->getSubject();
		if( $item instanceof Yncontest_Model_Entry)
		{			
			$item = Engine_Api::_()->getItem('contest', $item->contest_id);
		}
		$this->view->item = $item;
		
		$this->view->checkMaxEntries = Engine_Api::_()->yncontest()->checkMaxEntries(array(
				'contestId'=>$item->contest_id,
				'user_id' => $item->user_id,
		));
		
		//check Plugin
		switch ($item->contest_type) {
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
			case 'ynmusic':
				$plugin = Engine_Api::_()->yncontest()->getPluginsSocialMusic();
				break;
			case 'ynultimatevideo':
				$plugin = Engine_Api::_()->yncontest()->getPluginsUltimateVideo();
				break;
		}
		$this->view->plugin = true;
		if(empty($plugin)){
			$this->view->plugin = false;
		}		
		
		$this->view->announcement = Engine_Api::_()->getDbtable('announcements', 'yncontest')->getAnnouncementByContestId($item->contest_id);
	
	}


}