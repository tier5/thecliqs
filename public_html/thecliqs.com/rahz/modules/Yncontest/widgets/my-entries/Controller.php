<?php
class Yncontest_Widget_MyEntriesController extends Engine_Content_Widget_Abstract {
	
	public function indexAction()
  	{
  		//configure from back-end
  		$arrTemp['ynblog']['height'] = (int)$this -> _getParam('heightynblog',250);
		$arrTemp['ynblog']['width'] = (int)$this -> _getParam('widthynblog',90);
		$arrTemp['ynvideo']['height'] = (int)$this -> _getParam('heightynvideo',160);
		$arrTemp['ynvideo']['width'] = (int)$this -> _getParam('widthynvideo',155);
		$arrTemp['advalbum']['height'] = (int)$this -> _getParam('heightadvalbum',160);
		$arrTemp['advalbum']['width'] = (int)$this -> _getParam('widthadvalbum',155);		
		$arrTemp['mp3music']['height'] = (int)$this -> _getParam('heightmp3music',250);
		$arrTemp['mp3music']['width'] = (int)$this -> _getParam('widthmp3music',90);		
		$this->view->arrTemp = $arrTemp;
  		 
		$request = Zend_Controller_Front::getInstance() -> getRequest(); 	
		$this->view->form = $form = new Yncontest_Form_Entries_Search;	
		
		$params = $request->getParams ();
		$plugin = Engine_Api::_() -> yncontest() -> getPlugins();
		$defaultPlugin = '';
		if ($plugin) {
			$defaultPlugin = key($plugin);
		}
		$params['entry_type'] = $request->getParam('entry_type', $defaultPlugin);
		$form->populate($params);
	  
	    
	    if(empty($params['orderby'])) $params['orderby'] = 'modified_date';
	    if(empty($params['direction'])) $params['direction'] = 'DESC';
	    $this->view->formValues = $params;
		
		$viewer = Engine_Api::_()->user()->getViewer();
	 
	 	if($request->isPost()){ 
	      $values = $request->getPost();		
	      foreach ($values['delete'] as $value) {       
	          $entry = Engine_Api::_()->getItem('yncontest_entries', $value);          
	          if( $entry ){
	          	$entry->hidden = "1";		
				$entry->save();	  	
	          }         
	      }
	    }
		
		$params['user_id'] = $viewer->getIdentity();
		
		
		$this->view->arrPlugins = Engine_Api::_()->yncontest()->getPlugins();
	
		$this->view->formValues = $params;
		$this->view->paginator = $paginator = Engine_Api::_()->yncontest()->getEntryPaginator3($params);
		
		$items_per_page = Engine_Api::_()->getApi('settings', 'core')->getSetting('contest.entries.page',10);
		$this->view->paginator->setItemCountPerPage($items_per_page);
		if(isset($params['page'])) $this->view->paginator->setCurrentPageNumber($params['page']);    
	
		//$param['admin'] = 1;	 
	 }
}