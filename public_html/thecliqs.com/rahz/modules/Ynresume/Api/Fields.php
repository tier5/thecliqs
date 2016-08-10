<?php
class Ynresume_Api_Fields extends  Fields_Api_Core {
   
   public function getHeading()
   {
   		$type = 'ynresume_resume';
		return $this->getFieldsMeta($type)->getRowsMatching('type', 'heading'); 
   }
   
   public function getHeadingById($id) {
       $type = 'ynresume_resume';
        return $this->getFieldsMeta($type)->getRowMatching('field_id', $id);
   }
  
  public function checkHasQuestion($type, $heading,  $parent_field_id = null, $parent_option_id = null)
  {
	$isGet = false;
	$isStart = false;
    $structure = array();
    foreach( $this->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map ) {
     
	  // Skip maps that don't match parent_option_id (if provided)
      if( null !== $parent_option_id && $map->option_id != $parent_option_id ) {
        continue;
      }
      // Get child field
      $field = $this->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if( empty($field) ) {
        continue;
      }
	  
	  
	  if($field -> type == 'heading' && $field -> field_id == $heading)
	  {
		$isGet = true;
		$isStart = true;
		continue;
	  }
	  elseif($field -> type == 'heading' && $field -> field_id != $heading)
	  {
	  	if($isStart)
		{
		  	if($isGet)
			 {
			 	$isGet = false;
				break;
			 }
			 else 
			 {
				$isGet = true;
			 }
		}
	  }
	  
	  if($isGet)
	  {
	      return true;
	  }
    }
	
    return false;
  }
   
   public function getFieldTypeStr($spec)
   {
   	 return $this->getFieldType($spec);
   }
   
   public function getFieldIdsFullHeading($spec, $heading,  $parent_field_id = null, $parent_option_id = null)
  {
    $type = $this->getFieldType($spec);
	$isGet = false;
	$isStart = false;
    $structure = array();
    foreach( $this->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map ) {
     
	  // Skip maps that don't match parent_option_id (if provided)
      if( null !== $parent_option_id && $map->option_id != $parent_option_id ) {
        continue;
      }
      // Get child field
      $field = $this->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if( empty($field) ) {
        continue;
      }
	  
	  
	  if($field -> type == 'heading' && $field -> field_id == $heading)
	  {
		$isGet = true;
		$isStart = true;
		continue;
	  }
	  elseif($field -> type == 'heading' && $field -> field_id != $heading)
	  {
	  	if($isStart)
		{
		  	if($isGet)
			 {
			 	$isGet = false;
				break;
			 }
			 else 
			 {
				$isGet = true;
			 }
		}
	  }
	  
	  if($isGet)
	  {
	      // Add to structure
	      $structure[] = $field -> field_id;
	  }
    }
	
    return $structure;
  }
   
  public function getFieldsStructureFullHeading($spec, $heading,  $parent_field_id = null, $parent_option_id = null)
  {
    $type = $this->getFieldType($spec);
	$isGet = false;
	$isStart = false;
    $structure = array();
    foreach( $this->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map ) {
     
	  // Skip maps that don't match parent_option_id (if provided)
      if( null !== $parent_option_id && $map->option_id != $parent_option_id ) {
        continue;
      }
      // Get child field
      $field = $this->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if( empty($field) ) {
        continue;
      }
	  
	  
	  if($field -> type == 'heading' && $field -> field_id == $heading)
	  {
		$isGet = true;
		$isStart = true;
		continue;
	  }
	  elseif($field -> type == 'heading' && $field -> field_id != $heading)
	  {
	  	if($isStart)
		{
		  	if($isGet)
			 {
			 	$isGet = false;
				break;
			 }
			 else 
			 {
				$isGet = true;
			 }
		}
	  }
	  
	  if($isGet)
	  {
	      // Add to structure
	      $structure[$map->getKey()] = $map;
	      // Get children
	      if( $field->canHaveDependents() ) {
	        $structure += $this->getFieldsStructureFull($spec, $map->child_id);
	      }
	  }
    }
	
    return $structure;
  }
  
}
