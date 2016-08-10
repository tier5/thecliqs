<?php

class Ynbusinesspages_Model_DbTable_Pages extends Core_Model_DbTable_Pages {
	protected $_rowClass = 'Core_Model_Page';
	protected $_name = 'core_pages';
	protected static $_subject = null;

	protected function getSubject() {
		if (!self::$_subject) {
			$coreApi = Engine_Api::_() -> core();
			if ($coreApi -> hasSubject()) {
				self::$_subject = $coreApi -> getSubject();
			} else {
				self::$_subject = null;
			}
		}
		return self::$_subject;
	}

	public function loadContent(Engine_Content $contentAdapter, $name) {
		if (is_array($name)) {
			$name = join('_', $name);
		}
		if (!is_string($name) && !is_numeric($name)) {
			throw new Exception('not string');
		}

		$select = $this -> select() -> where('name = ?', $name) -> orWhere('page_id = ?', $name);
		$page = $this -> fetchRow($select);
        
		// check to hide menus
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$businessId = $business_session -> businessId;
		if($businessId)
		{
			if(in_array($name, array('header', 'header_mobi', 'footer', 'footer_mobi')))
			{
				return null;
			}
		}

		// check subject type and some thing else
		$page_id = $this -> getProxyPageId($page, $name);
        if(!$page_id) 
        {
            // throw?
            return null;
        }
        
		header($name . 'PAGE_ID: ' . $page_id);

		// Get all content
		$contentTable = Engine_Api::_() -> getDbtable('content', 'core');
		$select = $contentTable -> select() -> where('page_id = ?', $page_id) -> order('order ASC');
		$content = $contentTable -> fetchAll($select);
		// Create structure
		$structure = $this -> prepareContentArea($content);
		// Create element (with structure)
		$element = new Engine_Content_Element_Container( array('class' => 'layout_page_' . $page -> name, 'elements' => $structure));

		return $element;
	}

	public function getProxyPageId($page, $name) 
	{
		$page_id = $page -> page_id;
		if (is_string($name) && !in_array($name, array('header', 'footer', 'header_mobi', 'footer_mobi'))) 
		{
		    if($name == 'ynbusinesspages_profile_index')
            {
    			$hasSubject = Engine_Api::_() -> core() -> hasSubject();
    			if ($hasSubject) {
    				$subject = Engine_Api::_() -> core() -> getSubject();
    			}
    			if ($hasSubject && is_object($subject)) {
    				$subject_type = $subject -> getType();
    				$subject_id = $subject -> getIdentity();
    				if ($subject_type && $subject_id) {
    					$proxyTable = Engine_Api::_() -> getDbTable('proxies', 'ynbusinesspages');
    					$pageProxy = $proxyTable -> fetchRow($proxyTable -> select() -> where('subject_type=?', $subject_type) -> where('subject_id=?', $subject_id) -> where('page_name=?', $name));
    					if (is_object($pageProxy)) {
    						$page_id = $pageProxy -> page_id;
    					}
    				}
    			}
            }
			else if (Engine_Api::_() -> hasModuleBootstrap('ynmultilisting')) {
				$pagesName = Engine_Api::_()->ynmultilisting()->getPagesName();
			    if(in_array($name, $pagesName)) {
			    	$listingtype_id = Zend_Controller_Front::getInstance()->getRequest()->getParam('listingtype_id', 0);
					$newListingtype = Engine_Api::_()->getItem('ynmultilisting_listingtype', $listingtype_id);
		            if ($listingtype_id && $newListingtype && $newListingtype->show && ($current_listingtype_id != $listingtype_id)) {
		                Engine_Api::_()->ynmultilisting()->setCurrentListingType($listingtype_id);
		            }
	    			$listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
	    			if ($listingtype && is_object($listingtype)) {
	    				$subject_type = $listingtype -> getType();
	    				$subject_id = $listingtype -> getIdentity();
	    				if ($subject_type && $subject_id) {
	    					$proxyTable = Engine_Api::_() -> getDbTable('proxies', 'ynmultilisting');
	    					$pageProxy = $proxyTable -> fetchRow($proxyTable -> select() -> where('subject_type = ?', $subject_type) -> where('subject_id=?', $subject_id) -> where('page_name=?', $name));
	    					if (is_object($pageProxy)) { 
	    						$page_id = $pageProxy -> page_id;
	    					}
	    				}
	    			}
	            }
            }
            else if(Engine_Api::_() -> hasModuleBootstrap('ynsocialadspage'))
            {
                $pageProxy = Engine_Api::_() -> ynsocialadspage() -> getProxyObject($name);
                if (is_object($pageProxy))
                {
                    $page_id = $pageProxy -> page_id;
                }
            }
		}
		return $page_id;
	}

	public function getOriginalPage($page_name) {
		$pageTable = Engine_Api::_() -> getDbTable('pages', 'core');
		$orgPage = $pageTable -> fetchRow($pageTable -> select() -> where('name=?', $page_name));
		if (!is_object($orgPage)) {
			throw new Exception(sprintf("page name %s doesn't not exists!", $page_name));
		}
		return $orgPage;
	}

	public function getStructure($page_name) {
		try {
			$orgTable = $this -> getOriginalPage($page_name);
			$contentTable = Engine_Api::_() -> getDbtable('content', 'core');
			$select = $contentTable -> select() -> where('page_id = ?', $orgTable -> page_id) -> order('order ASC');
			$content = $contentTable -> fetchAll($select);
			$structure = $this -> prepareContentArea($content);
			return $structure;

		} catch(Exception $e) {
			echo $e -> getMessage();
		}
	}

	public function prepareContentArea($content, $current = null) {
		// Get parent content id
		$parent_content_id = null;
		if (null !== $current) {
			$parent_content_id = $current -> content_id;
		}

		// Get children
		$children = $content -> getRowsMatching('parent_content_id', $parent_content_id);
		if (empty($children) && null === $parent_content_id) {
			$children = $content -> getRowsMatching('parent_content_id', 0);
		}

		// Get struct
		$struct = array();
		foreach ($children as $child) {
			$elStruct = $this -> createElementParams($child);
			if ($elStruct) {
				$elStruct['elements'] = $this -> prepareContentArea($content, $child);
				$struct[] = $elStruct;
			}
		}

		return $struct;
	}

	public function createElementParams($row) {
		$data = array('identity' => $row -> content_id, 'type' => $row -> type, 'name' => $row -> name, 'order' => $row -> order, );
		$params = (array)$row -> params;
		if (isset($params['title'])) {
			$data['title'] = $params['title'];
		}
		$data['params'] = $params;
		
		// check to hide menus
		$business_session = new Zend_Session_Namespace('ynbusinesspages_business');
		$businessId = $business_session -> businessId;
		if($businessId)
		{
			if(in_array($row -> name, array('ynbusinesspages.main-menu')))
			{
				return FALSE;
			}
		}
		if (Engine_Api::_() -> core() -> hasSubject()) 
		{
			$subject = Engine_Api::_() -> core() -> getSubject();
			if($subject -> getType() == 'ynbusinesspages_business' && in_array($row -> name, array('activity.feed', 'ynfeed.feed')))
			{
				if(!$subject->isAllowed('comment'))
				{
					return FALSE;
				}
			}
		}
		return $data;
	}

}
