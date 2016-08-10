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
class Netlogtemplatedefault_Widget_NetlogLanguagesController extends Engine_Content_Widget_Abstract {

public function indexAction() {
		// Languages
	$translate    = Zend_Registry::get('Zend_Translate');
	$languageList = $translate->getList();

		// Prepare default langauge
	$defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
	if( !in_array($defaultLanguage, $languageList) ) {
		if( $defaultLanguage == 'auto' && isset($languageList['en']) ) {
			$defaultLanguage = 'en';
		} else {
			$defaultLanguage = null;
		}
	}

		// Prepare language name list
	$languageNameList  = array();
	$languageDataList  = Zend_Locale_Data::getList(null, 'language');
	$territoryDataList = Zend_Locale_Data::getList(null, 'territory');

	foreach( $languageList as $localeCode ) {
		$languageNameList[$localeCode] = Engine_String::ucfirst(Zend_Locale::getTranslation($localeCode, 'language', $localeCode));
		if (empty($languageNameList[$localeCode])) {
			if( false !== strpos($localeCode, '_') ) {
				list($locale, $territory) = explode('_', $localeCode);
			} else {
				$locale = $localeCode;
				$territory = null;
			}
			if( isset($territoryDataList[$territory]) && isset($languageDataList[$locale]) ) {
				$languageNameList[$localeCode] = $territoryDataList[$territory] . ' ' . $languageDataList[$locale];
			} else if( isset($territoryDataList[$territory]) ) {
				$languageNameList[$localeCode] = $territoryDataList[$territory];
			} else if( isset($languageDataList[$locale]) ) {
				$languageNameList[$localeCode] = $languageDataList[$locale];
			} else {
				continue;
			}
		}
	}

	if ( count($languageNameList) <= 1 )
		return $this->setNoRender();

	$languageNameList = array_merge(array($defaultLanguage => $defaultLanguage), $languageNameList);
	$this->view->languageNameList = $languageNameList;

  }

}