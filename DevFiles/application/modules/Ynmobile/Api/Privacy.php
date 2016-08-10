<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Privacy.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Privacy extends Core_Api_Abstract
{
	/**
	 * Input data:
	 *
	 * Output data:
	 * + sPhrase: string.
	 * + sValue: string.
	 *
	 * @see Mobile - API phpFox/Api V2.0
	 * @see privacy/privacy
	 *
	 * @param array $aData
	 * @return array
	 */
	public function privacy($aData)
	{
		return array(
			array(
				'sValue' => 'everyone',
				'sPhrase' => 'Everyone'
			),
			array(
				'sValue' => 'owner_network',
				'sPhrase' => 'Friends and Networks'
			),
			array(
				'sValue' => 'owner_member_member',
				'sPhrase' => 'Friends of Friends'
			),
			array(
				'sValue' => 'owner_member',
				'sPhrase' => 'Friends Only'
			),
			array(
				'sValue' => 'owner',
				'sPhrase' => 'Just Me'
			)
		);
	}

	public function allowedPrivacy($item = '', $settingKey = '')
	{
		if ($item == '' || $settingKey == '')
		{
			return $this->privacy();
		}
		else 
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			 // Prepare privacy options
		    $availableLabels = array(
		      'everyone'            => 'Everyone',
		      'owner_network'       => 'Friends and Networks',
		      'owner_member_member' => 'Friends of Friends',
		      'owner_member'        => 'Friends Only',
		      'owner'               => 'Just Me'
		    );
		
		    $options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed($item, $viewer, $settingKey);
		    $options = array_intersect_key($availableLabels, array_flip($options));
		    $result = array();
		    foreach ($options as $key => $value)
		    {
		    	$result[] = array(
					'sValue' => $key,
					'sPhrase' => $value
				);
		    }
		    return $result;
		}
	}
	
	public function simplePrivacy()
	{
		return array(
						'everyone' => 'Everyone',
						'owner_network' => 'Friends and Networks',
						'owner_member_member' => 'Friends of Friends',
						'owner_member' => 'Friends Only',
						'owner' => 'Just Me'
					);
	}
	
	
	
	
	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + sPhrase: string.
	 * + sValue: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see privacy/privacycomment
	 *
	 * @param array $aData
	 * @return array
	 */
	public function privacycomment($aData)
	{
		return $this -> privacy($aData);
	}

	/**
	 * Input data: N/A
	 *
	 * Output data:
	 * + iResourceId: int.
	 * + sName: string.
	 *
	 * @see Mobile - API SE/Api V2.0
	 * @see privacy/getfriends
	 *
	 * @param array $aData
	 * @return array
	 */
	public function getfriends($aData)
	{
		// Get viewer
		$viewer = Engine_Api::_() -> user() -> getViewer();
		// Multiple friend mode
		$membershipTable = Engine_Api::_() -> getDbTable('membership','user');
		$select = $viewer -> membership() -> getMembersOfSelect();
		$friends = $membershipTable->fetchAll($select);

		// Get stuff
		$ids = array();
		foreach ($friends as $friend)
		{
			$ids[] = $friend -> resource_id;
		}

		// Get the items
		$friendUsers = array();
		foreach (Engine_Api::_()->getItemTable('user')->find($ids) as $friendUser)
		{
			$friendUsers[] = array(
				'iResourceId' => $friendUser -> getIdentity(),
				'sName' => $friendUser -> getTitle(),
			);
		}
		return $friendUsers;
	}

}
