<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */


	echo $this->partial('_browse_folders.tpl', 'ynfilesharing',
		array('subFolders' => $this->folders, "files" => $this->files));
