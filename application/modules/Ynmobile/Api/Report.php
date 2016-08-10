<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2013 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: Report.php minhnc $
 * @author     MinhNC
 */

class Ynmobile_Api_Report extends Core_Api_Abstract
{
	/**
	 * Input data:
	 * + sCategory: int, required.
	 * + sDescription: string, required.
	 * + iItemId: int, required.
	 * + sItemType: string, required.
	 *
	 * Output data:
	 * + error_code: int.
	 * + error_message: string.
	 * + result: int.
	 * + message: string.
	 *
	 * @see Mobile - API SE/Api V1.0
	 * @see report/create
	 *
	 * @param array $aData
	 * @return array
	 */
	public function add($aData)
	{
		$sCategory = isset($aData['sCategory']) ? $aData['sCategory'] : '';
		$sDescription = isset($aData['sDescription']) ? $aData['sDescription'] : '';
		$iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
		$sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$viewer -> getIdentity() || !trim($sDescription) || !$sCategory || !$iItemId || !trim($sItemType))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid data 0')
			);
		}
		$subject = Engine_Api::_() -> getItem($sItemType, $iItemId);
		if (!$subject)
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid data 1')
			);
		}
		// Process
		$table = Engine_Api::_() -> getItemTable('core_report');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			$report = $table -> createRow();
			$report -> setFromArray(array(
				'category' => $sCategory,
				'description' => $sDescription,
				'subject_type' => $subject -> getType(),
				'subject_id' => $subject -> getIdentity(),
				'user_id' => $viewer -> getIdentity()
			));
			$report -> save();

			// Increment report count
			Engine_Api::_() -> getDbtable('statistics', 'core') -> increment('core.reports');
			$db -> commit();
			return array(
				'result' => 1,
				'message' => Zend_Registry::get('Zend_Translate') -> _('Your report has been submitted.')
			);
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('An error has occurred.')
			);
		}
	}

	/**
	 * Input data:
	 * + iItemId: int, required.
	 * + sItemType: string, required.
	 *
	 * Output data:
	 * + iReportId: int.
	 * + sDescription: string.
	 *
	 * @see Mobile - API SE/Api V3.0
	 * @see report/reason
	 *
	 * @param array $aData
	 * @return array
	 */
	public function reason($aData)
	{
		$sItemType = isset($aData['sItemType']) ? $aData['sItemType'] : '';
		$iItemId = isset($aData['iItemId']) ? (int)$aData['iItemId'] : 0;
		if (!$iItemId || !trim($sItemType))
		{
			return array(
				'error_code' => 1,
				'error_message' => Zend_Registry::get('Zend_Translate') -> _('Invalid data')
			);
		}
		$table = Engine_Api::_() -> getItemTable('core_report');
		$select = $table -> select() -> where("subject_id = ?", $iItemId) -> where("subject_type = ?", $sItemType);
		$aReasons = $table -> fetchAll($select);
		$aResult = array();
		foreach ($aReasons as $aReason)
		{

			$aResult[] = array(
				'iReportId' => $aReason -> report_id,
				'sDescription' => $aReason -> description
			);
		}
		return $aResult;
	}

}
