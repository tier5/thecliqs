<?php

class Ynmobile_Api_Statistic
{
	function info($aData)
	{
		$sItemType = isset($aData['sItemType']) ? 	$aData['sItemType'] : '';
		$iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
		
		$oItem = Engine_Api::_() -> getItem($sItemType, $iItemId);
		
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _("You don't have permission to delete this action!")
			);
		}
		
		$bCanComment = Engine_Api::_() -> authorization() -> isAllowed($oItem, null, 'comment');

		$response  = array(
			'sItemType'=> $sItemType,
			'iItemId'=> $iItemId,
			'aComments'=>array(),
			'bCanComment'=> $bCanComment,
			'aLikes'=>array(),
			'iTotalComment'=>0,
			'iTotalLike'=>0,
		);
		
		if(method_exists($oItem, 'comments')){
			$response['aComments'] = Engine_Api::_() -> getApi('comment','ynmobile') -> listallcomments(array_merge($aData, array('iLimit'=>3)));
			$response['iTotalComment'] =  $oItem->comments()->getCommentCount();
		}
		
		if(method_exists($oItem, 'likes')){
				
			$likes  =  $oItem -> likes();
			
			$response['iTotalLike']=  $likes-> getLikeCount();
			
			$response['bIsLike'] =  $likes->isLike($viewer);
			
			$response['aLikes'] = Engine_Api::_()->getApi('like','ynmobile') -> getUserLike($oItem);
		}
		
		return $response;
	}
	
}
