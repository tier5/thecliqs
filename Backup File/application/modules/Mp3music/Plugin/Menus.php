<?php
class Mp3music_Plugin_Menus
{
	public function onMenuInitialize_UserProfileSubscribeMp3($row)
	{
		// Check viewer
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( !$viewer->getIdentity() ) {
	      return false;
	    }
	
	    // Check subject
	    if( !Engine_Api::_()->core()->hasSubject() ) {
	      return false;
	    }
    
		$subject = Engine_Api::_() -> core() -> getSubject();
		$label = "Subscribe Mp3 Music";
		if ($viewer -> isSelf($subject))
		{
			return false;
		}
		if ($subject -> authorization() -> isAllowed($viewer, 'view'))
		{
			$subscriptionTable = Engine_Api::_()->getDbtable('subscriptions', 'mp3music');
    		if( !$subscriptionTable->checkSubscription($subject, $viewer) ) 
    		{
    			$label = "Subscribe Mp3 Music";
				$icon = 'application/modules/Mp3music/externals/images/subscribe.png';
				$route = 'mp3music_subscribe';
			}
			else 
			{
				$label = "Unsubscribe Mp3 Music";
				$icon = 'application/modules/Mp3music/externals/images/unsubscribe.png';
				$route = 'mp3music_unsubscribe';
			}
			return array(
				'label' => $label,
				'icon' => $icon,
				'route' => $route,
				'class' => 'buttonlink smoothbox',
				'params' => array(
					'user_id' => ($viewer -> getGuid(false) == $subject -> getGuid(false) ? null : $subject -> getIdentity()),
				)
			);
		}

		return false;
	}

}
