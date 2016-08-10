<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Profile.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Profile extends Core_Api_Abstract
{
	
	function detail($aData){
		
		$info = $this->info($aData);
		
		$profile = Engine_Api::_()->getApi('user','ynmobile')->profile($aData);
		
		$info['BasicInfo'] = $profile;
        
        $injector =  Ynmobile_Api_Injector::__();
        
        $oUser =  Engine_Api::_()->user()->getUser($aData['iUserId']);
        
        $injector->dataFriend($info, $oUser, 3);
        $injector->dataAlbum($info, $oUser, 3);
        
		return $info;
	}
	
	/**
	 * Profile info.
	 *
	 * Input data:
	 * + iUserId: int, required.
	 *
	 * @param array $aData
	 * @return array
	 */
	public function info($aData)
	{
		$oViewer = Engine_Api::_() -> user() -> getViewer();
		if (!$oViewer -> getIdentity())
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to view this profile!")
			);
		}
		extract($aData, EXTR_SKIP);
		/**
		 * @var int
		 */
		$iUserId = isset($iUserId) ? (int)$iUserId : $oViewer -> getIdentity();
		$oUser = Engine_Api::_() -> user() -> getUser($iUserId);

		if (!$oUser)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("Profile is not valid!")
			);
		}

		// if ($oViewer->isBlockedBy($oUser))
		// {
			// return array(
					// 'error_code' => 2,
					// 'error_message' => Zend_Registry::get('Zend_Translate') -> _("You do not have permission to view this private page!")
			// );
		// }
		
		// Load fields view helpers
		$view = Zend_Registry::get('Zend_View');
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

		// Values
		$fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($oUser);
		
		// Calculate viewer-subject relationship
		$usePrivacy = ($oUser instanceof User_Model_User);
		if ($usePrivacy)
		{
			$relationship = 'everyone';
			if ($oViewer && $oViewer -> getIdentity())
			{
				if ($oViewer -> getIdentity() == $oUser -> getIdentity())
				{
					$relationship = 'self';
				}
				else
				if ($oViewer -> membership() -> isMember($oUser, true))
				{
					$relationship = 'friends';
				}
				else
				{
					$relationship = 'registered';
				}
			}
		}
		$show_hidden = $oViewer -> getIdentity() ? ($oUser -> getOwner() -> isSelf($oViewer) || 'admin' === Engine_Api::_() -> getItem('authorization_level', $oViewer -> level_id) -> type) : false;
		$sProfileImage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_ICON);
		$sProfileBigImage = $oUser -> getPhotoUrl(TYPE_OF_USER_IMAGE_PROFILE);
		if ($sProfileImage != "")
		{
			$sProfileImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileImage);
			$sProfileBigImage = Engine_Api::_() -> ynmobile() ->finalizeUrl($sProfileBigImage);
		}
		else
		{
			$sProfileImage = NO_USER_ICON;
			$sProfileBigImage = NO_USER_NORMAL;
		}
        $aProfileInfo= array(
            'BasicInfo'=>array(),
            'About_Me'=>array(),
            'Details'=>array(),
        );
        
        
		$aProfileInfo['BasicInfo']['Profile_Image'] = $sProfileImage;
		$aProfileInfo['BasicInfo']['Profile_Image_Big'] = $sProfileBigImage;
		$aProfileInfo['BasicInfo']['Display_Name'] = $oUser -> getTitle();
        
		foreach ($fieldStructure as $index => $map)
		{
			$field = $map -> getChild();
			$value = $field -> getValue($oUser);
			if (!$field || $field -> type == 'profile_type')
				continue;
			if (!$field -> display && !$show_hidden)
				continue;
			$isHidden = !$field -> display;

			// Get first value object for reference
			$firstValue = $value;
			if (is_array($value))
			{
				$firstValue = $value[0];
			}

			// Evaluate privacy
			if ($usePrivacy && !empty($firstValue -> privacy) && $relationship != 'self')
			{
				if ($firstValue -> privacy == 'self' && $relationship != 'self')
				{
					$isHidden = true;
					//continue;
				}
				else
				if ($firstValue -> privacy == 'friends' && ($relationship != 'friends' && $relationship != 'self'))
				{
					$isHidden = true;
					//continue;
				}
				else
				if ($firstValue -> privacy == 'registered' && $relationship == 'everyone')
				{
					$isHidden = true;
					//continue;
				}
			}
			if ((!$isHidden || $show_hidden) && $firstValue)
			{
				$value = $firstValue -> value;
				switch ($field -> type)
				{
					case 'first_name' :
						$aProfileInfo['BasicInfo']['First_Name'] =  $value;
						break;
					case 'last_name' :
						$aProfileInfo['BasicInfo']['Last_Name'] =  $value;
						break;
					case 'gender' :
						$gender = "";
						if ($value == 2)
							$gender = 'Male';
						elseif ($value == 3)
						{
							$gender = 'Female';
						}
						$aProfileInfo['BasicInfo']['Gender'] = $gender;
						break;
					case 'birthdate' :
						$aProfileInfo['BasicInfo']['Date_Of_Birth_YMD'] = ($value) ? date('Y-m-d', strtotime($value)) : "";
						$aProfileInfo['BasicInfo']['Date_Of_Birth'] = ($value) ? date('M j, Y', strtotime($value)) : "";
						break;
					case 'relationship_status' :
						$aProfileInfo['BasicInfo']['Relationship_Status'] = $this -> getRelation($value);
						break;
					case 'zip_code' :
						$aProfileInfo['BasicInfo']['Zip_Postal_Code'] = $value;
						break;
					case 'city' :
						$aProfileInfo['BasicInfo']['City'] = $value;
						break;
					case 'location' :
						$aProfileInfo['BasicInfo']['Location'] = $value;
						break;
					case 'about_me' :
						$aProfileInfo['About_Me']['About_Me'] = $value;
						break;
					default :
						$aProfileInfo['Details'][$field -> type] = $value;
						break;
				}
			}
		}
		return $aProfileInfo;
	}
	/**
	 * @param String
	 * @return String
	 */
	public function getRelation($name)
	{
		$view = Zend_Registry::get('Zend_View');
		$title = "";
		switch ($name)
		{
			case 'single' :
				$title = $view -> translate('Single');
				break;
			case 'relationship' :
				$title = $view -> translate('In a Relationship');
				break;
			case 'engaged' :
				$title = $view -> translate('Engaged');
				break;
			case 'married' :
				$title = $view -> translate('Married');
				break;
			case 'complicated' :
				$title = $view -> translate("It's Complicated");
				break;
			case 'open' :
				$title = $view -> translate('In an Open Relationship');
				break;
			case 'widow' :
				$title = $view -> translate('Widowed');
				break;
		}
		return $title;
	}

}
