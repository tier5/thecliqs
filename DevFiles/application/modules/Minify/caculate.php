<?php

$result = array();
$result['error']= false;
$result['message']= '';
$config =  Engine_Api::_()->minify()->readMinifySetting();

$js1 =  isset($config['home']['js'])?$config['home']['js']:array();
$js2 =  isset($config['member_home']['js'])?$config['member_home']['js']:array();
$js3 =  isset($config['home_profile']['js'])?$config['home_profile']['js']:array();

$js0 =  array();
$js0 =  array_unique(array_merge($js0, $js1, $js2, $js3));

$groups =  array();
$index =  0;

foreach($js0 as $js){
    // $flag = true => include $js to groups    
    $flag = true;
    $mess =  'js'.($index+1);
        
  if(isset($groups[$index]) && count($groups[$index]) > 25){
        $index ++;
    }else if(stripos($js,'jquery') !== false){
        $flag = false;
        $index ++;
    }else if(stripos($js,'tinymce') !== false){
        $flag = false;        
    }else if(stripos($js,'smoothbox4.js')!= false){
    	$index ++;
    }
    /* 
	 * If not is core module or yn module it will be insert into other group
	 */
    else
	    if(
	    		(stripos($js,'application/modules/') !== false)	    		
				&&
				(stripos($js,'application/modules/Core/') === false)
				&&
				(stripos($js,'application/modules/User/') === false)
				&& 
	    		(stripos($js,'application/modules/Activity/') === false)				
				&&
				(stripos($js,'application/modules/Yn') === false)
				&&
				(stripos($js,'application/modules/Socialstore/') === false)
				
		)
	    {
	    	$flag = false;	    	
	    }
    if($flag){
        $groups[$mess][] = $js;    
    }   
}

$groups['js1'][] = 'application/modules/Core/externals/scripts/composer.js';

$css1 =  isset($config['home']['css'])?$config['home']['css']:array();
$css2 =  isset($config['member_home']['css'])?$config['member_home']['css']:array();
$css3 =  isset($config['home_profile']['css'])?$config['home_profile']['css']:array();

$css0 =  array();
$css0 =  array_unique(array_merge($css0, $css1, $css2, $css3));
$index =  0;
foreach($css0 as $css){
    // $flag = true => include $css to groups    
    $flag = true;
    $mess =  'css'.($index+1);
        
    if(stripos($css,'smoothbox4.css') !== false){
        $index ++;    
    }else if(count($groups[$index]) > 40){
        $index ++;
    }else if(stripos($css,'jquery') !== false){
        $flag = false;
        $index ++;
    }else if(stripos($css,'tinymce') !== false){
        $flag = false;
    }
		
    if($flag){
        $groups[$mess][] = $css;    
    }   
}

$config['groups']  = $groups;

if(!empty($groups)){
    Engine_Api::_()->minify()->writeMinifySetting($config);    
}

$result['groups'] = $groups;
$result['keys'] =  array_keys($groups);

$settings = Engine_Api::_()->getApi('settings', 'core');
$counter = $settings->core_site_counter;

if( !$counter ) {
  $settings->core_site_counter = $counter = 1;
}

$settings->setSetting('core.site.counter', $counter+1);

echo Zend_Json::encode($result);
