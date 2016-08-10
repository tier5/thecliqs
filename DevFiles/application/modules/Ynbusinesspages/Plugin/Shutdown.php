<?php
class Ynbusinesspages_Plugin_Shutdown extends Zend_Controller_Plugin_Abstract
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		// CHECK IF ADMIN
		if (substr($request -> getPathInfo(), 1, 5) == "admin")
		{
			return;
		}
		$view = Zend_Registry::get('Zend_View');
		$module = $request -> getModuleName();
		$controller = $request -> getControllerName();
		$action = $request -> getActionName();
		
		$key = 'ynbusinesspages_predispatch_url:' . $module . '.' . $controller . '.' . $action;
		if (isset($_SESSION[$key]) && $_SESSION[$key]) 
		{
			$url = $_SESSION[$key];
			header('location:' . $url);
			unset($_SESSION[$key]);
			@session_write_close();
			exit ;
		}
		
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$businessId = $business_session -> businessId;
		if(!$businessId)
		{
			return;
		}
		$business = Engine_Api::_() -> getItem('ynbusinesspages_business', $businessId);
		// check and redirect to business manin page
		$redirect = true;
		$subjectId = 0;
		switch ($module) 
		{
			case 'ynbusinesspages':
				switch ($controller) 
				{
					case 'profile':
						$subjectId = $request -> getParam('id', 0);
						break;
					case 'review':
						$subjectId = $businessId;
						break;
                    case 'contact':
                        $subjectId = $businessId;
                        break;
					case 'transaction':
						$subjectId = $businessId;
                        break;
					case 'photo':
					case 'dashboard':
                    case 'announcement':
					case 'layout':
					case 'member':
					case 'post':
					case 'topic':
					case 'video':
					case 'music':
					case 'event':
					case 'wiki':
					case 'file':
					case 'groupbuy':
					case 'contest':
					case 'classified':
					case 'poll':
					case 'blog':
					case 'listings':
					case 'job':
					case 'business':
					case 'compare':
					case 'social-music':
					case 'ultimate-video':
						$subjectId = $request -> getParam('business_id', 0);
						if (!$subjectId && $strSubject = $request -> getParam('subject', ''))
						{
							$arrSubject = explode('_', $strSubject);
							if(current($arrSubject) == 'ynbusinesspages')
							{
								$subjectId = end($arrSubject);
							}
						}
						break;
					case 'index':
						if(in_array($action, array('direction', 'logout-business', 'warning', 'place-order', 'update-order', 'pay-credit', 'compose-message')))
						{
							$subjectId = $businessId;
						}
						break;
				}
			break;
			case 'ynpayment':
			case 'activity':
			case 'ynfeed':
				$subjectId = $businessId;
				break;
			case 'core':
				if(in_array($controller, array('widget', 'tag', 'comment', 'report', 'link')))
                {
                    $subjectId = $businessId;
                }
                break;
            case 'album':
            case 'advalbum':
			case 'video':
			case 'ynvideo':
			case 'music':
			case 'mp3music':
			case 'event':
			case 'ynevent':
			case 'ynfilesharing':
			case 'ynwiki':
			case 'ynlistings':
			case 'groupbuy':
			case 'blog':
			case 'ynblog':
			case 'classified':
			case 'yncontest':
            case 'yncredit':
            case 'ynjobposting':
            case 'poll': 
            case 'socialpublisher':
			case 'ynmusic':
			case 'ynultimatevideo':
				if(in_array($action, array('create', 'create-contest')) && $request -> getParam('parent_type', '') == 'ynbusinesspages_business')
				{
					$subjectId = $request -> getParam('business_id', $request -> getParam('subject_id', 0));
				}
				if(in_array($action, array('validation', 'edit', 'delete','append','change')))
				{
					$subjectId = $businessId;
				}
				// video
				if(in_array($action, array('upload-video', 'compose-upload')))
				{
					$subjectId = $businessId;
				}
				
				// file sharing
				if(in_array($controller, array('folder', 'file')) || ($controller == 'index' && in_array($action, array('move', 'share')))) {
					$subjectId = $businessId;
                }
                
                //yncontest
                if(($controller == 'payment-paypal') || ($controller == 'my-setting' && $action == 'create-contest-setting') || ($controller == 'my-contest' && $action == 'publish') || ($controller == 'payment' && $action == 'method')) {
                    $redirect = false;
                }
                
                //yncredit
                if ($controller == 'spend-credit') {
                    $redirect = false;
                }
                
                //groupbuy
                if(($controller == 'index' && in_array($action, array('success', 'publish', 'publish-free', 'publishmoney'))) || ($controller == 'photo') || ($controller == 'payment' && $action == 'method')) {
                    $redirect = false;
                }
                
                //ynlistings
                if(($controller == 'transaction') || ($controller == 'index' && in_array($action, array('place-order', 'update-order', 'pay-credit', 'get-my-location'))) || ($controller == 'video' && $action == 'suggest')) {
                    $redirect = false;
                }
                
                //mp3music
                if($controller == 'album' && in_array($action, array('upload-song', 'edit-add-song', 'compose-upload'))) {
                    $redirect = false;
                }
                
                //music
                if($controller == 'playlist' && in_array($action, array('add-song'))) {
                    $redirect = false;
                }
                
                //social publisher
                if ($controller == 'share') {
                    $redirect = false;
                }
				
				//ynmusic
                if (in_array($controller, array('songs', 'albums', 'playlists', 'index')) && !in_array($action, array('index', 'listing'))) {
                    $redirect = false;
                }
                break;
		}	
		
		if(($action == 'view' && !in_array($controller, array('photo', 'topic', 'folder')))
			|| ($controller == 'profile' && $action == 'index' && in_array($module, array('event', 'ynevent')))
			|| ($controller == 'view')
            || ($controller == 'album' && $action == 'album'
			|| ($controller == 'profile' && $action == 'index' && $module == 'ynbusinesspages' && $subjectId != $businessId)
			)
			)
		{
			$return_url = '64-' . base64_encode($_SERVER['REQUEST_URI']);
			$url = $view -> url(array(
						'action' => 'warning',
						'subject' => $business->getGuid(),
						'return_url' => $return_url,
					), 'ynbusinesspages_general', true);
			header('location:' . $url);
			exit;
		}
		
		if($subjectId == $businessId)
		{
			$redirect = false;
		}
		if($redirect)
		{
			header('location:' . $business -> getHref());
			exit;
		}
	}
}