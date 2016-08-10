<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

require_once (APPLICATION_PATH .  '/application/modules/Ynfilesharing/controllers/Scribd/scribd.php');

class Ynfilesharing_Plugin_Job_Upload extends Core_Plugin_Job_Abstract {
	
	protected function _execute() 
	{
		// Get job and params
		$job = $this->getJob();
	
		// No file id?
		if (!($file_id = $this->getParam('file_id'))) {
			$this->_setState('failed', 'No file identity provided.');
			$this->_setWasIdle();
			return;
		}
	
		// Get file object
		$file = Engine_Api::_()->getItem('ynfilesharing_file', $file_id);
		if (!$file || !($file instanceof Ynfilesharing_Model_File)) {
			$this->_setState('failed', 'File is missing.');
			$this->_setWasIdle();
			return;
		}
	
		/*
		// Check file status
		if (0 != $file->status) {
			$this->_setState('failed', 'Document has already been uploaded to Scribd, or has already failed uploading.');
			$this->_setWasIdle();
			return;
		}
		*/
		
		// Process
		try {
			$this->_process($file);
			$this->_setIsComplete(true);
		} catch (Exception $e) {
			$this->_setState('failed', 'Exception: ' . $e->getMessage());
	
			// Attempt to set file state to failed
			try {
				if (1 != $file->status) {
					$file->status = 3; //uploaded fail
					$file->save();
				}
			} catch (Exception $e) {
				$this->_addMessage($e->getMessage());
			}
		}
	}
	
	
	protected function _process($file) 
	{
		$file->status = 2;//processing status
		$file->save();
		
		$settings = Engine_Api::_ ()->getApi ( 'settings', 'core' );
		$scribd_api_key = $settings->getSetting ( 'ynfilesharing.apikey' );
		$scribd_secret = $settings->getSetting ( 'ynfilesharing.apisecret' );
		
		//echo $scribd_api_key . "    " . $scribd_secret; exit;
		
		if ($scribd_api_key == null || $scribd_secret == null) {
			throw new Exception("Missing Scribd api key or secret");
		}
		
		$folder = Engine_Api::_ ()->getItem ( 'folder', $file->folder_id );
		$file_path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $folder->path . $file->name;
		
		$scribd = new Scribd ( $scribd_api_key, $scribd_secret );
		$is_success = 1;
		$status = 'PROCESSING';
		
		$doc_type = null;
		$access = 'private';
		$rev_id = null;
		
		try 
		{
			$db = Engine_Api::_ ()->getDbtable ( 'documents', 'ynfilesharing' )->getAdapter ();
			$is_uploaded = Engine_Api::_ ()->getDbtable ( 'documents', 'ynfilesharing' )->checkFileUploaded ( $file->getIdentity () );
		
			if (! $is_uploaded) {
				$data = $scribd->upload ( $file_path, $doc_type, $access, $rev_id );

				if (is_array ( $data )) {
					$tbl_documents = Engine_Api::_ ()->getDbtable ( 'documents', 'ynfilesharing' );
					
					$row = $tbl_documents->createRow ();
					$row->document_id = $file->getIdentity ();
					$row->doc_id = $data ['doc_id'];
					$row->access_key = $data ['access_key'];
					if ($data ['secret_password']) {
						$row->secret_password = $data ['secret_password'];
					}
					$row->save ();
					$is_success = 1;
				}
				else {
					$is_success = 0;
				}
			}
			
			// file is existed in database
			if ($is_uploaded) {
				// check if file is existed on Scribd
				$document = Engine_Api::_ ()->getItem ( 'ynfilesharing_document', $file->getIdentity () );
				$data = $scribd->getSettings ( $document->doc_id );
				
				if (! is_array ( $data )) {
					$data = $scribd->upload ( $file_path, $doc_type, $access, $rev_id );
					if (is_array ( $data )) {
						$document->doc_id = $data ['doc_id'];
						$document->access_key = $data ['access_key'];
						if ($data ['secret_password']) {
							$document->secret_password = $data ['secret_password'];
						}
						$document->save ();
						$is_success = 1;
					}
					else {
						$is_success = 0;
					}
				}
			}
			
			$status = $scribd->getConversionStatus ( $document->doc_id );
		
			if ($is_success == 1) {
				$file->status = 1;
				$file->save();
			}
		}
		 
		catch ( exception $ex ) 
		{
			$is_success = 0;
		}
	}
}