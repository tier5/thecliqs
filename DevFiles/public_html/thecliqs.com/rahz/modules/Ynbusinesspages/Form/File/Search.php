<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynbusinesspages_Form_File_Search extends Engine_Form {
	/* ----- Init Form Function ----- */
	public function init() {
		
		$translate = Zend_Registry::get("Zend_Translate");
		
		$this->setAttribs ( array (
			'id' => 'ynbusinesspages_filesharing_filter_form',
			'class' => 'global_form_box',
			'style' => 'margin-bottom: 15px',
			'method' => 'GET'
		))->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
		
		// Text filter element
		$this->addElement ( 'Text', 'search', array (
			'label' => 'Search',
			'alt' => $translate -> translate('Search folder'),
			'onchange' => 'this.form.submit();'
		) );
		
		// Browse By Filter Element
		$this->addElement ( 'Select', 'type', array (
			'label' => 'Type',
			'multiOptions' => array (
				'all' => 'All',
				'folder' => 'Folder',
				'file' => 'File'
			),
			'onchange' => 'this.form.submit();'
		) );
		
		// Browse By Filter Element
		$this->addElement ( 'Select', 'orderby', array (
			'label' => 'List By',
			'multiOptions' => array (
				'creation_date' => 'Most Recent',
				'view_count' => 'Most Viewed',
				'download_count' => 'Most Downloaded'
			),
			'onchange' => 'this.form.submit();'
		) );

		$param = Zend_Controller_Front::getInstance()->getRequest()->getParam('type');
		if ($param == 'folder' || $param == '') {
			unset($this->orderby->options['download_count']);
		}
	}
}