<?php

class Yncontest_View_Helper_FormContestContestMultiLevel extends Zend_View_Helper_Abstract{
	
	public function formContestContestMultiLevel($name, $value = null, $attributes = array()){
		
		$xhtml =  array();
		
		// CODE HERE
		$xhtml[] =  '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
		
		$model_class = $attributes['model'];
		
		if(!$model_class){
			throw new Exception("attribute model is required");
		}
// 		if ($model_class == 'Socialstore_Model_DbTable_Customcategories') {
// 			$store_id = $attributes['store_id'];
// 			$model =  new $model_class;
// 			$select = $model->select()->where('store_id = ?', $store_id)->where('customcategory_id = ?', $value);
// 			$item = $model->fetchRow($select);
// 		}
// 		else {
			$model =  new $model_class;
			$item =  $model->find((string)$value)->current();
// 		}
		$level =  0;

		
		
		if(!is_object($item)){
// 			if ($model_class == 'Socialstore_Model_DbTable_Customcategories') {
// 				$store_id = $attributes['store_id'];
// 				$options =  $model->getMultiOptions(0,$store_id);
// 			}
// 			else {
				$options =  $model->getMultiOptions(0);
// 			}
			$i= 0;
			$element = new Zend_Form_Element_Select(
				sprintf("%s_%s",$name, 0),
				array(
					'multiOptions'=> $options,
					'onchange'=>$attributes['onchange'],
				)
			); 	
			$xhtml[] =  '<div id="id_wrapper_'.$name.'_'.$i.'">'. $element->renderViewHelper() .'</div>';
			$i = 1;
		}else{
			$level  = $item->getLevel();			
			for($i =  0; $i< $level; ++$i){
// 				if ($model_class == 'Socialstore_Model_DbTable_Customcategories') {
// 					$store_id = $attributes['store_id'];
// 					$options =  $model->getMultiOptions($item->getIndexTree($i),$store_id);
// 				}
// 				else {
					$options =  $model->getMultiOptions($item->getIndexTree($i));
// 				}
				if ($model_class == 'Yncontest_Model_DbTable_Locations') {
					$element = new Zend_Form_Element_Select(
						sprintf("%s_%s",$name, $i),
						array(
							'multiOptions'=> $options,
							'onchange'=>$attributes['onchange'],
							'value'=>$item->{'p'.($i+1)}
						)
					);
				}
				else {
					$element = new Zend_Form_Element_Select(
						sprintf("%s_%s",$name, $i),
						array(
							'multiOptions'=> $options,
							'onchange'=>$attributes['onchange'],
							//'value'=>$item->getNextValueMultiSelect($i)
							'value'=>$item->{'p'.($i+1)}
						)
					);
				} 	
				if ($i !=0) {
					$style = 'style="margin-top: 8px;"';
				}
				else {
					$style = '';
					
				}
				$xhtml[] =  '<div ' . $style.' id="id_wrapper_'.$name.'_'.$i.'" >'. $element->renderViewHelper() .'</div>';
			}
		}
		
		$level =  $model->getMaxLevel();
		for(; $i<$level; ++$i){
			$xhtml[] =  '<div id="id_wrapper_'.$name.'_'.$i.'" style = "display: none">'.'<!-- wrapper at level '.$i.'-->'  .'</div>';
		}
				
		$xhtml =  implode("",$xhtml);
		return $xhtml;
	}
}
