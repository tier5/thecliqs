<?php
class Ynmobile_Installer extends Engine_Package_Installer_Module
{
	public function onInstall()
	{
		// Run upgrades first to prevent issues with upgrading from older versions
		parent::onInstall();
		$this->editVideoTbl();
		$this->editChatTbl();
	}
	
	public function editVideoTbl()
	{
		$sql = "ALTER TABLE `engine4_video_videos` ADD COLUMN `file1_id` INT(11) DEFAULT '0' NULL AFTER `file_id`";
		$db = $this -> getDb();
		try {
			$info = $db -> describeTable('engine4_video_videos');
			if ($info && !isset($info['file1_id']))
			{
				try
				{
					$db -> query($sql);
				}
				catch( Exception $e )
				{
				}
			}
		}
		catch (Exception $e)
		{
		}
		
		$sql = "ALTER TABLE `engine4_video_videos` ADD COLUMN `status_text` TEXT NULL AFTER `description`";
		try {
			$info = $db -> describeTable('engine4_video_videos');
			if ($info && !isset($info['status_text']))
			{
				try
				{
					$db -> query($sql);
				}
				catch( Exception $e )
				{
				}
			}
		}
		catch (Exception $e)
		{
		}
	}
	
	public function editChatTbl()
	{
		$db = $this -> getDb();
		try 
		{
			$info = $db -> describeTable('engine4_chat_whispers');
			if ($info && !isset($info['read']))
			{
				try
				{
					$sql = "ALTER TABLE `engine4_chat_whispers` ADD COLUMN `read` TINYINT(1) DEFAULT '1' NULL AFTER `sender_deleted`";
					$db -> query($sql);
				}
				catch( Exception $e )
				{
				}
			}
		} 
		catch (Exception $e) 
		{
		}	
	}
}
