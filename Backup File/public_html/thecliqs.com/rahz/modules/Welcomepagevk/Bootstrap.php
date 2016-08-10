<?php
/**
 * @category   Application_Core
 * @package    Welcomepagevk
 * @copyright  Copyright 2011 SocialEngineMarket
 * @license    http://www.socialenginemarket.com
*/
class Welcomepagevk_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
  
    parent::__construct($application);  
    $file = APPLICATION_PATH . '/application/settings/database.php';
    $options = include $file;

    $db = Zend_Db::factory($options['adapter'], $options['params']);
    //$db = $this->getDb();
    
	/*
	$select6 = new Zend_Db_Select($db);
    $select6
      ->from('engine4_core_modules')
      ->where('name = ?', 'Theme');	
	$info6 = $select6->query()->fetch();
	if ($info6)
	{
		$db->delete('engine4_core_modules',
			array(
				'name = ?' => 'Theme'
				)					
			);		
	}
*/



    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_themes')
      ->where('name = ?', 'vk');	
	$info = $select->query()->fetch();

	if ($info)
	{
		if ($info['active']==0)
		{
			$select1 = new Zend_Db_Select($db);
			$select1
			  ->from('engine4_core_settings')
			  ->where('name = ?', 'welcomepagevk.enable');	
			$info1 = $select1->query()->fetch();
			if ($info1)
			{
				if ($info1['value']==1)
				{
					
					$db->update('engine4_core_settings', 
						array(
							'value' => '0',
						), 
						array(
							'name = ?' => 'welcomepagevk.enable'
						)
					
					);
					
					$db->delete('engine4_core_content',
						array(
							'name = ?' => 'welcomepagevk.redirect'
						)					
					);
					
				}
			}
			//print_r($info1);
			//echo '<br>!!!!!!!!!!!!!!!<br>';
		}
		if ($info['active']==1)		
		{
			$select2 = new Zend_Db_Select($db);
			$select2
			  ->from('engine4_core_content')
			  ->where('name = ?', 'welcomepagevk.footer-url');	
			$info2 = $select2->query()->fetch();

			if( empty($info2) ) 
			{
				$select3 = new Zend_Db_Select($db);
				$select3
					->from('engine4_core_pages')
					->where('name = ?', 'footer');
				$info3 = $select3->query()->fetch();      
				$footer_page_id = $info3['page_id'];
				  
				$select4 = new Zend_Db_Select($db);
				$select4
					->from('engine4_core_content')
					->where('page_id = ?', $footer_page_id)
					->where('type = ?', 'container')
					->where('name = ?', 'main');
					
				$info4 = $select4->query()->fetch();      
				$parent_footer_content_id = $info4['content_id'];	  
				

				// widget footer url
				$db->insert('engine4_core_content', array(
					'page_id' => $footer_page_id,
					'type' => 'widget',
					'name' => 'welcomepagevk.footer-url',
					'parent_content_id' => $parent_footer_content_id,
					'order' => 3,
					'params' => '',
				));				
			}
		}
	}
	
	else
	{

		$select5 = new Zend_Db_Select($db);
		$select5
		  ->from('engine4_core_settings')
		  ->where('name = ?', 'welcomepagevk.enable');	
		$info5 = $select5->query()->fetch();

		if ($info5)
		{
			if ($info5['value']==1)
			{
					
				$db->update('engine4_core_settings', 
					array(
						'value' => '0',
					), 
					array(
						'name = ?' => 'welcomepagevk.enable'
					)
						
				);
				
				$db->delete('engine4_core_content',
					array(
						'name = ?' => 'welcomepagevk.redirect'
					)					
				);
						
			}
		}	  
	}	
    //print_r($info);
	//die();
  }  
}
