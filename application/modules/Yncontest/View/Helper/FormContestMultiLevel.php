<?php

class Yncontest_View_Helper_FormContestMultiLevel extends Zend_View_Helper_Abstract{
	
	public function formContestMultiLevel($name, $value = null, $attributes = array()){
		
		$xhtml =  array();
		
		// CODE HERE
		$xhtml[] =  '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
		
		$model_class = $attributes['model'];
		$contest_id = 0;
		if(!empty($attributes['contest_id']))
		{
			$contest_id = $attributes['contest_id'];
		}
		
		if(!$model_class){
			throw new Exception("attribute model is required");
		}
		
		$model =  new $model_class;
		$item =  $model->find((string)$value)->current();
		$level =  0;
		if(!is_object($item))
		{
			$options =  $model->getMultiOptions(0, $contest_id);
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
		}
		else
		{
			$level  = $item->getLevel();
			for($i =  0; $i< $level; ++$i)
			{
				$options =  $model->getMultiOptions($item->getIndexTree($i),$contest_id);
				
				$element = new Zend_Form_Element_Select(
						sprintf("%s_%s",$name, $i),
						array(
								'multiOptions'=> $options,
								'onchange'=>$attributes['onchange'],
								'value'=>$item->getNextValueMultiSelect($i)
						)
				);
				
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
