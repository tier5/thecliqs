<?php
//RUNNING URL: <SITE_URL>?m=lite&module=ynfilesharing&name=fixdata


//Fix data for table "files"
$tbl = Engine_Api::_()->getDbTable("files","ynfilesharing");
$files = $tbl -> fetchAll();
$output = "<br />--------------CHECKING FILES------------------<br />";
foreach ($files as $file)
{	
	if (empty($file->parent_type)) 
	{
		$output .= $file->getTitle();
		$file->delete();
		$output .= " - missing Parent Type..........(Deleted)<br />";
		continue;
	}
	else
	{
		$type = $file->parent_type;
	}
	
	if (empty($file->parent_id))
	{
		$output .= $file->getTitle();
		$file->delete();
		$output .= " - missing Parent ID..........(Deleted)<br />";
		continue;
	}
	else
	{
		$id = $file->parent_id;
	}
	
	if( !(($parent = Engine_Api::_()->getItem($type, $id)) instanceof Core_Model_Item_Abstract) || !$parent->getIdentity() ) {
		$output .= $file->getTitle();
		$file->delete();
		$output .= " - missing Parent ..........(Deleted)<br />";
		continue;
	}
	
	$folder = $file->getParentFolder();
	
	if (!is_object($folder) || $folder == null){
		$output .= $file->getTitle();
		$file->delete();
		$output .= " - no parent folder ..........(Deleted)<br />";
		continue;
	}
	
	$isExist =  (file_exists($folder->path . $file->name));
	if (!$isExist)
	{
		$output .= $file->getTitle();
		$file->delete();
		$output .= " - is not existed ..........(Deleted)<br />";
		continue;
	}
}

//Fix data for table "folders"
$output .= "<br />--------------CHECKING FOLDERS------------------<br />";
$tbl = Engine_Api::_()->getDbTable("folders","ynfilesharing");
$folders = $tbl -> fetchAll();

foreach ($folders as $folder)
{
	if (empty($folder->parent_type))
	{
		$output .= $folder->getTitle();
		$folder->delete();
		$output .= " - missing Parent Type..........(Deleted)<br />";
		continue;
	}
	else
	{
		$type = $folder->parent_type;
	}

	if (empty($folder->parent_id))
	{
		$output .= $folder->getTitle();
		$folder->delete();
		$output .= " - missing Parent ID..........(Deleted)<br />";
		continue;
	}
	else
	{
		$id = $folder->parent_id;
	}

	if( !(($parent = Engine_Api::_()->getItem($type, $id)) instanceof Core_Model_Item_Abstract) || !$parent->getIdentity() ) {
		$output .= $folder->getTitle();
		$folder->delete();
		$output .= " - missing Parent ..........(Deleted)<br />";
		continue;
	}
	
	$isExist =  (file_exists($folder->path) && is_dir($folder->path));
	if (!$isExist)
	{
		$output .= $folder->getTitle();
		$folder->delete();
		$output .= " - is not existed ..........(Deleted)<br />";
		continue;
	}
	
}

echo $output; exit;


