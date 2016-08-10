<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplatedefault
 * @copyright  Copyright 2010-2012 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     altrego aka Vadim ( provadim@gmail.com )
 */

class NetlogDefault_Installer extends Engine_Package_Installer_Theme {

  function onInstall() {

	$db = $this->getDb();

	parent::onInstall();

		// change active theme to netlog
	$info = $db->select()
		->from('engine4_core_themes', array('name'))
		->where('name = ?', 'netlog-default')
		->query()->fetch();
	$theme_exists = $info['name'];

	if ( !empty($theme_exists) ) {
		$db->update( 'engine4_core_themes', array( 'active' => 0 ) );
		$db->update( 'engine4_core_themes', array( 'active' => 1 ), 'name = "netlog-default"' );
	}
  }
}