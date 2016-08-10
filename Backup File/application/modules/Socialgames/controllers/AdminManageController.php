<?php
class Socialgames_AdminManageController extends Core_Controller_Action_Admin {

	public function indexAction()
	{				
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('socialgame_admin_main', array(), 'socialgames_admin_manage');
		
		$import = $this->_getParam('import');
		$table = Engine_Api::_()->getDbtable('games', 'socialgames');
		$games = $table->fetchAll();
		if ($games->count()==0 and !$import)
		{
			$this->view->import = true;
			return false;
		}
		$this->view->form = $form = new Socialgames_Form_Admin_Games_Search();
		
		if($form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
        }
		
		$page = $this->_getParam('page',1);
		$this->view->paginator = Engine_Api::_()->getDbtable('games', 'socialgames')->getGamesPaginator(array(
		  'orderby' => 'games_id',
		  'search' => $values["search"]
		));
		if ($values["search"])
		{
			$limit = 100;
		}
		else
		{
			$limit = 25;
		}
		$this->view->paginator->setItemCountPerPage($limit);
		$this->view->paginator->setCurrentPageNumber($page);
		
		if ($import)
		{
			$viewer = Engine_Api::_()->user()->getViewer();
			$xml = simplexml_load_file("http://www.kongregate.com/games_for_your_site.xml") or die("Error: Cannot create object");
			$goodgames = array(
				0 =>  array(
					"title" =>  "GOODGAME EMPIRE",
					"description" =>  "66 millions player by world.Goodgame Empire is an awesome multiplayer strategy game made by Goodgame Studios. Build a castle, create a powerful army and test your battle skills in player vs. player fights. Is your Empire strong enough? Much Fun! ",
					"image" =>  "http://media.goodgamestudios.com/www/publishers/images/empire180x135.jpg",
					"flash" =>  "http://empire.goodgamestudios.com/"
				),
				1 =>  array(
					"title" =>  "SHADOW KINGS - DARK AGES",
					"description" =>  "Shadow Kings is a new game developed by Goodgame Studios, which released very successful games as the Empire, Big Farm (Farmer) and Gangster (Mafia). That game look like Empire, but everything go on popular fantasy world and that's big advantage. By the way, the first minutes of gameplay look like much better when I thought. ",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/shadowkings_darkages_desktop_640x288.jpg",
					"flash" =>  "http://shadowkings.goodgamestudios.com/"
				),
				"2" => array(
					"title" =>  "GOODGAME BIG FARM",
					"description" =>  "Big Farm is an awesome multiplayer farm management game made by the Goodgame Studios. Your mission is simple: Create a big farm, grow crops, breed animals, and become the richest farmer of the universe. Enjoy Goodgame's Big Farm!",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/big_farm_desktop_640x288.jpg",
					"flash" =>  "http://bigfarm.goodgamestudios.com/"
				),
				"3" => array(
					"title" =>  "GOODGAME GALAXY",
					"description" =>  "Goodgame Galaxy is a futuristic mass multiplayer strategy game where you are the serving commander of a new space station far, far away. Your mission is to build a base and conquer the galaxy. Enjoy!",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/galaxy_desktop_640x288.jpg",
					"flash" =>  "http://galaxy.goodgamestudios.com/"
				),
				"4" => array(
					"title" =>  "GOODGAME GANGSTER",
					"description" =>  "Create your own bandit and make him the best of all baddies. To increase your skills and standing do jobs for the godfather, gain experience and fight other gangsters from all around the world. Hope you all enjoy this game!",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/gangster_desktop_640x288.jpg",
					"flash" =>  "http://gangster.goodgamestudios.com/"
				),
				"5" => array(
					"title" =>  "GOODGAME POKER",
					"description" =>  "Play poker with players around the world. Create your custom avatar and play Texas Hold poker rules given in the two hole cards and five cards!",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/poker_desktop_640x288.jpg",
					"flash" =>  "http://poker.goodgamestudios.com/"
				),
				"6" => array(
					"title" =>  "GOODGAME CAFE",
					"description" =>  "Goodgame Cafe is an addictive restaurant management game from the Goodgame Studios. Open your own Cafe and amaze your friends and customers with your baking and cooking skills. Decide what's on the menu today, buy new ingredients for cookies and grow your business. Read the tutorials to understand the game basics. ",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/cafe_desktop_640x288.jpg",
					"flash" =>  "http://cafe.goodgamestudios.com/"
				),
				"7" =>  array(
					"title" =>  "GOODGAME FASHION",
					"description" =>  "Goodgame Fashion is an awesome fashion shop management game. Become a famous designer and make your fashion shop the hottest and most stylish place in town. Buy fabrics and produce trendy clothes for men and women.",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/fashion_desktop_640x288.jpg",
					"flash" =>  "http://fashion.goodgamestudios.com/"
				),
				"8" => array(
					"title" =>  "GOODGAME DISCO",
					"description" =>  "Goodgame Disco is an addictive nightclub management game from the GoodGame Studios. Be a famous DJ and choose the best music for your Disco or be a barkeeper and celebrate the ritual of mixing cocktails. Create the hottest club in town!",
					"image" =>  "http://static.goodgamestudios.com/wp-content/uploads/2014/09/disco_desktop_640x288.jpg",
					"flash" =>  "http://disco.goodgamestudios.com/"
				)
			);
			$db = $table->getAdapter();
			$db->beginTransaction();
			try{
				foreach($xml as $item)
				{
					
					$addMenuItem = $table->createRow();
					$addMenuItem->setFromArray(array("title" => (string)$item->title,
							'description' => (string)$item->description,
							'launch' => (string)$item->launch_date,
							'play_count' => (string)$item->gameplays,
							'image' => (string)$item->thumbnail,
							'flash' => (string)$item->flash_file,
							'instruction' => (string)$item->instructions,
							'is_active' => 1,
							'category' => (string)$item->category,
							'user_id' => $viewer->getIdentity()
						));
					$addMenuItem->save();
					
					$auth = Engine_Api::_()->authorization()->context;
					$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

					$auth_view = "everyone";

					$auth_comment = "everyone";

					$viewMax = array_search($auth_view, $roles);
					$commentMax = array_search($auth_comment, $roles);
					foreach ($roles as $i => $role) {
						$auth->setAllowed($addMenuItem, $role, 'view', ($i <= $viewMax));
						$auth->setAllowed($addMenuItem, $role, 'comment', ($i <= $commentMax));
					}
				}
				foreach($goodgames as $item)
				{
					$addMenuItem = $table->createRow();
					$addMenuItem->setFromArray(array(
							'title' => (string)$item["title"],
							'description' => (string)$item["description"],
							'launch' => (string)$item["launch_date"],
							'play_count' => (string)$item["gameplays"],
							'image' => (string)$item["image"],
							'flash' => (string)$item["flash"],
							'instruction' => (string)$item["instructions"],
							'is_active' => 1,
							'category' => 'GoodGames',
							'user_id' => $viewer->getIdentity()
					));
					$addMenuItem->save();
					$auth = Engine_Api::_()->authorization()->context;
					$roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'registered', 'everyone');

					$auth_view = "everyone";

					$auth_comment = "everyone";

					$viewMax = array_search($auth_view, $roles);
					$commentMax = array_search($auth_comment, $roles);
					foreach ($roles as $i => $role) {
						$auth->setAllowed($addMenuItem, $role, 'view', ($i <= $viewMax));
						$auth->setAllowed($addMenuItem, $role, 'comment', ($i <= $commentMax));
					}
				}
				$db->commit();
				return $this->_helper->redirector->gotoRoute(array('action' => 'index','import'=>0));
			}
			catch (Exception $error){
				$db->rollBack();
				throw $error;
			}
		}
	}
	
	
	public function levelsAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('socialgame_admin_main', array(), 'socialgames_admin_managelevels');
			
		if(null !== ($id = $this->_getParam('id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }
		
		if(!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }
		
		$level_id = $id = $level->level_id;

        $this->view->form = $form = new Socialgames_Form_Admin_Games_Level(array(
            'public' => (in_array($level->type, array('public'))),
            'moderator' => (in_array($level->type, array('admin', 'moderator')))
        ));

        $form->level_id->setValue($id);

        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $form->populate($permissionsTable->getAllowed('socialgames_game', $id, array_keys($form->getValues())));

        if(!$this->getRequest()->isPost()) {
            return;
        }
        if(!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        $values = $form->getValues();

        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();

        try {
            $permissionsTable->setAllowed('socialgames_game', $id, $values);
            $db->commit();
            
        } catch (exception $e) {
            $db->rollBack();
            throw $e;
        }
        $form->addNotice('Your changes have been saved.');
	}
	
	public function gameeditAction(){
		$this->view->id = $id = $this->_getParam('id');
		$mainMenuTable = Engine_Api::_()->getDbtable('games', 'socialgames');
		$mainMenuItem = $mainMenuTable->find($id)->current();
		$this->view->form = $form = new Socialgames_Form_Admin_Games_Manage();
		if ($this->_isFormValid($form) && !empty($id)){
			$mainMenuItem->setFromArray($form->getValues());
			$mainMenuItem->save();
			$this->_thisForward('edited');
			}
		else{
			$form->populate($mainMenuItem->toArray());
		}
	}

	public function addmenumanageAction(){
		$this->view->addmenu = $addMenu = Engine_Api::_()->getDbtable('addmenus', 'creative')->getForAdmin();
	}

	public function addmenucreateAction(){
		$this->view->form = $form = new Creative_Form_Admin_Addmenu_Manage();
		if ($this->_isFormValid($form)){
			$addMenuTable = Engine_Api::_()->getDbtable('addmenus', 'creative');
			$addMenuItem = $addMenuTable->createRow();
			$db = $addMenuTable->getAdapter();
			$db->beginTransaction();
			try{
				$addMenuItem->setFromArray($form->getValues());
				$addMenuItem->save();
				$db->commit();
			}
			catch (Exception $error){
				$db->rollBack();
				throw $error;
			}
			$this->_thisForward('added');
		}
	}

	public function addmenueditAction(){
		$this->view->id = $id = $this->_getParam('id');
		$addMenuTable = Engine_Api::_()->getDbtable('addmenus', 'creative');
		$addMenuItem = $addMenuTable->find($id)->current();
		$this->view->form = $form = new Creative_Form_Admin_Addmenu_Manage();
		$form->setTitle('Edit Add Menu');
		if ($this->_isFormValid($form) && !empty($id)){
			$addMenuItem->setFromArray($form->getValues());
			$addMenuItem->save();
			$this->_thisForward('edited');
			}
		else{
			$form->populate($addMenuItem->toArray());
		}
	}

	public function gamedeleteAction()
	{
		$id = $this->_getParam('id', null);
		$addMenuTable = Engine_Api::_()->getDbtable('games', 'socialgames');
		$addMenuItem = $addMenuTable->find($id)->current();

		$this->view->form = $form = new Socialgames_Form_Admin_Games_Delete();

		if ($this->getRequest()->isPost())
		{
			$db = $addMenuTable->getAdapter();
			$db->beginTransaction();

			try
			{
				$addMenuItem->delete();
				$db->commit();
			}
			catch (Exception $error)
			{
				$db->rollBack();
				throw $error;
			}

			$this->_thisForward('deleted');
		}
	}
	public function addmenuorderAction(){
		if( !$this->getRequest()->isPost() ){
			return;
		}
		$table = Engine_Api::_()->getDbtable('addmenus', 'creative');
		$addmenuItems = $table->fetchAll();
		foreach( $addmenuItems as $addmenuItem ) {
			$order = $this->getRequest()->getParam('admin_menus_item_'.$addmenuItem->addmenu_id);
			if( !$order ){
				$order = 999;
			}
			$addmenuItem->order = $order;
			$addmenuItem->save();
		}
		return;
	}

	public function enableaddmenuAction(){
		$id = $this->_getParam('id', null);
		$this->view->form = $form = new Creative_Form_Admin_Addmenu_Disable();
		$form->setTitle('Enable item?');
		$form->setDescription('Are you sure you want to enable this item?');
		$form->getElement('enabled')->setLabel('Enable');
		if ($this->getRequest()->isPost()){
			Engine_Api::_()->getDbtable('addmenus', 'creative')->update(array(
				'enabled' => 1
			), array('addmenu_id = ?' => $id));

			$this->_thisForward('enabled');
		}
	}

	public function disableaddmenuAction(){
		$id = $this->_getParam('id', null);
		$this->view->form = $form = new Creative_Form_Admin_Addmenu_Disable();
		if ($this->getRequest()->isPost()){
			Engine_Api::_()->getDbtable('addmenus', 'creative')->update(array(
				'enabled' => 0
			), array('addmenu_id = ?' => $id));

			$this->_thisForward('disabled');
		}
	}

	public function slideshowmanageAction(){
		$this->view->slides = Engine_Api::_()->getDbtable('slides', 'creative')->getForAdmin();
	}

	public function slidecreateAction(){
		$this->view->form = $form = new Creative_Form_Admin_Slide_Manage();
		if ($this->_isFormValid($form)){
			
			$slideTable = Engine_Api::_()->getDbtable('slides', 'creative');
			$slideItem = $slideTable->createRow();
			$db = $slideTable->getAdapter();
			$db->beginTransaction();
			try{
				$slideItem->setFromArray($form->getValues());
				$slideItem->save();
				$slideItem->addFile($form->slide_image);
				$db->commit();
			}
			catch (Exception $error){
				$db->rollBack();
				throw $error;
			}
			$this->_thisForward('added');
		}
	}

	public function slideeditAction(){
		$id = $this->_getParam('id', null);
		$slideTable = Engine_Api::_()->getDbtable('slides', 'creative');
		$slideItem = $slideTable->find($id)->current();
		$rgb = $this->_rgbDecToHex($slideItem['slide_bg']);
		$this->view->form = $form = new Creative_Form_Admin_Slide_Manage();
		$form->setTitle('Edit Slide');
		$form->getElement('slide_image')->setRequired(false);
		if ($this->_isFormValid($form) && !empty($id)) {
			if ($form->getValue('slide_image')) {
				$slideItem->removeFile();
				$slideItem->addFile($form->slide_image);
			} else {
				$slideItem->setFromArray($form->getValues());
				$slideItem->save();
			}
			$this->_thisForward('edited');
		} else {
			$form->populate($slideItem->toArray());
		}
	}

	public function slidedeleteAction() {
		$id = $this->_getParam('id', null);
		$slideTable = Engine_Api::_()->getDbtable('slides', 'creative');
		$slideItem = $slideTable->find($id)->current();
		$this->view->form = $form = new Creative_Form_Admin_Slide_Delete();
		if ($this->getRequest()->isPost()) {
			$db = $slideTable->getAdapter();
			$db->beginTransaction();
			try {
				$slideItem->removeFile();
				$slideItem->delete();
				$db->commit();
			} catch (Exception $error) {
				$db->rollBack();
				throw $error;
			}
			$this->_thisForward('deleted');
		}
	}

	public function slideorderAction(){
		if( !$this->getRequest()->isPost() ){
			return;
		}
		$table = Engine_Api::_()->getDbtable('slides', 'creative');
		$slideItems = $table->fetchAll();
		foreach( $slideItems as $slideItem ) {
			$order = $this->getRequest()->getParam('admin_menus_item_'.$slideItem->slide_id);
			if( !$order ){
				$order = 999;
			}
			$slideItem->order = $order;
			$slideItem->save();
		}
		return;
	}

	public function enableslideAction(){
		$id = $this->_getParam('id', null);
		$this->view->form = $form = new Creative_Form_Admin_Slide_Disable();
		$form->setTitle('Enable item?');
		$form->setDescription('Are you sure you want to enable this item?');
		$form->getElement('enabled')->setLabel('Enable');
		if ($this->getRequest()->isPost()){
			Engine_Api::_()->getDbtable('slides', 'creative')->update(array(
				'enabled' => 1
			), array('slide_id = ?' => $id));

			$this->_thisForward('enabled');
		}
	}

	public function disableslideAction(){
		$id = $this->_getParam('id', null);
		$this->view->form = $form = new Creative_Form_Admin_Slide_Disable();
		if ($this->getRequest()->isPost()){
			Engine_Api::_()->getDbtable('slides', 'creative')->update(array(
				'enabled' => 0
			), array('slide_id = ?' => $id));

			$this->_thisForward('disabled');
		}
	}

	public function socialsmanageAction(){
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->view->form = $form = new Creative_Form_Admin_Socials_Manage();
		if ($this->_isFormValid($form)){
			$settings->setSetting('creative.flickr.url', $form->getValue('flickr'));
			$settings->setSetting('creative.twitter.url', $form->getValue('twitter'));
			$settings->setSetting('creative.facebook.url', $form->getValue('facebook'));
			$settings->setSetting('creative.dribbble.url', $form->getValue('dribbble'));
			$settings->setSetting('creative.google.url', $form->getValue('google'));
			
		}
		else{
			$form->populate($form->getValues());
		}
	}

	public function featuresmanageAction(){
		$this->view->features = Engine_Api::_()->getDbtable('features', 'creative')->getFeatures();
	}

	public function featureeditAction(){
		$id = $this->_getParam('id', null);
		$featureTable = Engine_Api::_()->getDbtable('features', 'creative');
		$featureItem = $featureTable->find($id)->current();
		$rgb = $this->_rgbDecToHex($featureItem['feature_bg']);
		$this->view->form = $form = new Creative_Form_Admin_Feature_Manage();
		if ($this->_isFormValid($form) && !empty($id)) {
			$rgb = $form->getValue('feature_bg');
			$form->getElement('feature_bg')->setValue($this->_rgbHexToDec($rgb));
			$featureItem->setFromArray($form->getValues());
			$featureItem->save();
			$this->_thisForward('edited');
		}
		else {
			$form->populate($featureItem->toArray());
			$form->getElement('feature_bg')->setValue($rgb);
		}
	}

	public function featureorderAction(){
		if( !$this->getRequest()->isPost() ){
			return;
		}
		$table = Engine_Api::_()->getDbtable('features', 'creative');
		$featureItems = $table->fetchAll();
		foreach( $featureItems as $featureItem ) {
			$order = $this->getRequest()->getParam('admin_menus_item_'.$featureItem->feature_id);
			if( !$order ){
				$order = 999;
			}
			$featureItem->order = $order;
			$featureItem->save();
		}
		return;
	}

	public function settingsmanageAction(){
		$settings = Engine_Api::_()->getApi('settings', 'core');
		$this->view->form = $form = new Creative_Form_Admin_Setting_Manage();
		if ($this->_isFormValid($form)){
			$settings->setSetting('creative.maincolor', $form->getValue('maincolor'));
			$settings->setSetting('creative.footercolor', $form->getValue('footercolor'));
			$settings->setSetting('creative.headercolor', $form->getValue('headercolor'));
		}
		else{
			$form->populate($form->getValues());
		}
		$settings = Engine_Api::_()->getApi('settings', 'core');
	}
	public function setPhoto($photo)
    {
        if( $photo instanceof Zend_Form_Element_File ) {
			  $file = $photo->getFileName();
			  $fileName = $file;
			} else if( $photo instanceof Storage_Model_File ) {
			  $file = $photo->temporary();
			  $fileName = $photo->name;
			} else if( $photo instanceof Core_Model_Item_Abstract && !empty($photo->file_id) ) {
			  $tmpRow = Engine_Api::_()->getItem('storage_file', $photo->file_id);
			  $file = $tmpRow->temporary();
			  $fileName = $tmpRow->name;
			} else if( is_array($photo) && !empty($photo['tmp_name']) ) {
			  $file = $photo['tmp_name'];
			  $fileName = $photo['name'];
			} else if( is_string($photo) && file_exists($photo) ) {
			  $file = $photo;
			  $fileName = $photo;
			} else {
			  //throw new Classified_Model_Exception('invalid argument passed to setPhoto');
			}

			if( !$fileName ) {
			  $fileName = basename($file);
			}

			$extension = ltrim(strrchr(basename($fileName), '.'), '.');
			$base = rtrim(substr(basename($fileName), 0, strrpos(basename($fileName), '.')), '.');
			$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
			
			$params = array(
			  'parent_type' => "creativedesign",
			  'parent_id' => "34sf",
			  'name' => $fileName,
			);

			// Save
			$filesTable = Engine_Api::_()->getItemTable('storage_file');

			// Resize image (main)
			$mainPath = $path . DIRECTORY_SEPARATOR . $base . '_m.' . $extension;
			$image = Engine_Image::factory();
			$image->open($file)
			  ->write($mainPath)
			  ->destroy();

			

			// Store
			$iMain = $filesTable->createFile($mainPath, $params);
			
			
			// Remove temp files
			@unlink($mainPath);
    
        return $iMain->getPhotoUrl();
    }
	private function _isFormValid($form){
		return $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost());
	}

	private function _thisForward($message)
	{
		return $this->_forward('success', 'utility', 'core', array(
					'smoothboxClose' => 10,
					'parentRefresh' => 10,
					'messages' => array($this->view->translate('This item has been successfully ' . $message . '.'))
		));
	}

	private function _rgbHexToDec($rgb_hex) {
		$rgb_dec = hexdec(substr($rgb_hex, 0, 2));
		for ($i = 1; $i < 3; $i++) {
			$rgb_dec .= "," . hexdec(substr($rgb_hex, 2 * $i, 2));
		}
		return $rgb_dec;
	}

	private function _rgbDecToHex($rgb_dec) {
		list($r, $g, $b) = split('[,]', $rgb_dec);
		$rgb_hex = dechex($r) . dechex($g) . dechex($b);
		if(strlen($rgb_hex)<6){
			if(substr($rgb_hex, 0, 1) == '0'){
				$rgb_hex =  str_pad($rgb_hex,6,0,STR_PAD_LEFT);
			}
			else{
				$rgb_hex =  str_pad($rgb_hex,6,0,STR_PAD_RIGHT);
			}
		}
		return $rgb_hex;
	}

	private function _getHover($color){
		switch ($color) {
			case '#ff0097':
				$hover = '#ec0990';
				break;
			case '#603cba':
				$hover = '#54389c';
				break;
			case '#2d89ef':
				$hover = '#2a7dd8';
				break;
			case '#ffc40d':
				$hover = '#e8b30f';
				break;
			case '#99b433':
				$hover = '#879f2e';
				break;
			case '#00a300':
				$hover = '#018c01';
				break;
			case '#1e7145':
				$hover = '#195c38';
				break;
			case '#7e3878':
				$hover = '#6c3167';
				break;
			case '#1d1d1d':
				$hover = '#000000';
				break;
			case '#00aba9':
				$hover = '#009a98';
				break;
			case '#2b5797':
				$hover = '#274e86';
				break;
			case '#e3a21a':
				$hover = '#cb9119';
				break;
			case '#da532c':
				$hover = '#c84e2b';
				break;
			case '#ee1111':
				$hover = '#d51111';
				break;
			case '#b91d47':
				$hover = '#a01b3f';
				break;
		}
		return $hover;
	}
}