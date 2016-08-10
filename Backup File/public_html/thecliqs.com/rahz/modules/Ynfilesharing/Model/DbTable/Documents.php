<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynfilesharing_Model_DbTable_Documents extends Engine_Db_Table {
	protected $_rowClass = "Ynfilesharing_Model_Document";

	public function checkFileUploaded($file_id){
		$file_row = Engine_Api::_ ()->getItem ( 'ynfilesharing_document', $file_id);
		if (is_object ( $file_row )) {
			return TRUE;
		}
		return FALSE;
	}
}
