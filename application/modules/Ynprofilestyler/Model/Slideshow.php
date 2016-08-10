<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Model_Slideshow extends Core_Model_Item_Abstract
{
	public function addSlide($url) {
		$slideTable = new Ynprofilestyler_Model_DbTable_Slides();
		$slide = $slideTable->createRow(array(
			'slideshow_id' => $this->getIdentity(),
			'url' => $url,
			'creation_date' => date('Y-m-d H:i:s')
		));
		$slide->save();

		return $this;
	}

	public function getSlides($isPublished = NULL) {
		$slideTable = new Ynprofilestyler_Model_DbTable_Slides();
		$select = $slideTable->select()->where('slideshow_id = ?', $this->getIdentity());
		if ($isPublished != NULL) {
			$select->where('published = ?', $isPublished);
		}
		$select->order('published DESC');

		return $slideTable->fetchAll($select);
	}

	public function deleteSlides($slideIds) {
		$slideTable = new Ynprofilestyler_Model_DbTable_Slides();
		return $slideTable->delete(array(
			'slide_id in (?)' => $slideIds,
			'slideshow_id = ?' => $this->getIdentity()
		));
	}
	
	public function publishSlides($slideIds, $published) {
		$slideTable = new Ynprofilestyler_Model_DbTable_Slides();
		return $slideTable->update(
			array('published' => $published), 
			array(
				'slide_id in (?)' => $slideIds,
				'slideshow_id = ?' => $this->getIdentity()
			)
		);
	}
}