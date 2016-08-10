<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
class Ynultimatevideo_VideoController extends Core_Controller_Action_Standard
{
	public function init()
	{
		// Must be able to use videos
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynultimatevideo_video', null, 'view') -> isValid())
		{
			return;
		}

		// Get subject
		$video = null;
		$id = $this -> _getParam('video_id', $this -> _getParam('id', null));
		if ($id)
		{
			$video = Engine_Api::_() -> getItem('ynultimatevideo_video', $id);
			if ($video)
			{
				Engine_Api::_() -> core() -> setSubject($video);
			}
		}

		// Require subject
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}

		// Require auth
		if (!$this -> _helper -> requireAuth() -> setAuthParams($video, null, 'view') -> isValid())
		{
			return;
		}
	}

	public function embedAction()
	{

		// Get subject
		$this -> view -> video = $video = Engine_Api::_() -> core() -> getSubject('ynultimatevideo_video');

		// Check if embedding is allowed
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynultimatevideo.embeds', 1))
		{
			$this -> view -> error = 1;
			return;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$this -> view -> error = 2;
			return;
		}

		// Get embed code
		$this -> view -> embedCode = $video -> getEmbedCode();
	}

	public function externalAction()
	{
		// Get subject
		$this -> view -> video = $video = Engine_Api::_() -> core() -> getSubject('ynultimatevideo_video');

		// Check if embedding is allowed
		if (!Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynultimatevideo.embeds', 1))
		{
			$this -> view -> error = 1;
			return;
		}
		else
		if (isset($video -> allow_embed) && !$video -> allow_embed)
		{
			$this -> view -> error = 2;
			return;
		}

		// Get embed code
		$embedded = "";
		if ($video -> status == 1)
		{
			$video -> view_count++;
			$video -> save();
			$embedded = $video -> getRichContent(true);
		}

		// Track views from external sources
		Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('ynultimatevideo.embedviews');

		// Get file location
		if ($video -> type == 3 && $video -> status == 1)
		{
			if (!empty($video -> file_id))
			{
				$storage_file = Engine_Api::_() -> getItem('storage_file', $video -> file_id);
				if ($storage_file)
				{
					$this -> view -> video_location = $storage_file -> map();
				}
			}
		}

		$this -> view -> rating_count = Engine_Api::_() -> ynultimatevideo() -> ratingCount($video -> getIdentity());
		$this -> view -> video = $video;
		$this -> view -> videoEmbedded = $embedded;
		if ($video -> category_id != 0)
		{
			$this -> view -> category = Engine_Api::_() -> ynultimatevideo() -> getCategory($video -> category_id);
		}
	}

	public function sendToFriendsAction() {
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();

		$this->view->video = $video = Engine_Api::_()->getItem('ynultimatevideo_video', $this -> _getParam('id'));
		if (!$video) {
			return $this->_helper->requireSubject()->forward();
		}
		$this->view->form = $form = new Ynultimatevideo_Form_SendToFriends();

		if (!$this -> getRequest() -> isPost()) {
			return;
		}

		if (!$form -> isValid($this -> getRequest() -> getPost())) {
			return;
		}
		$values = $form -> getValues();
		$sentEmails = $video -> sendEmailToFriends($values['recipients'], @$values['message']);

		$message = Zend_Registry::get('Zend_Translate') -> _("$sentEmails email(s) have been sent.");
		return $this -> _forward('success', 'utility', 'core', array(
			'parentRefresh' => false,
			'smoothboxClose' => true,
			'messages' => $message
		));
	}
}
