<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: IndexController.php
 * @author     Minh Nguyen
 */
class Ynauction_IndexController extends Core_Controller_Action_Standard
{
	protected $_paginate_params = array();
	public function init()
	{
		$this -> view -> viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$this -> _paginate_params['limit'] = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.page', 10);
		$this -> _paginate_params['sort'] = $this -> getRequest() -> getParam('sort', 'recent');
		$this -> _paginate_params['page'] = $this -> getRequest() -> getParam('page', 1);
		$this -> _paginate_params['search'] = $this -> getRequest() -> getParam('search', '');
	}

	public function browseAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main', array(), 'ynauction_main_browse');
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function listingAction()
	{
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function createAction()
	{
		$this -> _helper -> content -> setEnabled();
		// Check auth
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams('ynauction_product', null, 'create') -> isValid())
			return;
		if (!Engine_Api::_() -> ynauction() -> checkBecome($viewer -> getIdentity()))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$this -> view -> form = $form = new Ynauction_Form_Create();
		$supportedCurrencyIndex = array();
		$fullySupportedCurrencies = array();
		$supportedCurrencies = array();
		$gateways = array();
		$gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		foreach ($gatewaysTable->fetchAll() as $gateway)
		{
			$gateways[$gateway -> gateway_id] = $gateway -> title;
			$gatewayObject = $gateway -> getGateway();
			$currencies = $gatewayObject -> getSupportedCurrencies();
			if (empty($currencies))
			{
				continue;
			}
			$supportedCurrencyIndex[$gateway -> title] = $currencies;
			if (empty($fullySupportedCurrencies))
			{
				$fullySupportedCurrencies = $currencies;
			}
			else
			{
				$fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
			}
			$supportedCurrencies = array_merge($supportedCurrencies, $currencies);
		}
		$supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

		$translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
		$fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
		$supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
		$form -> currency_symbol -> setMultiOptions(array(
			'Fully Supported' => $fullySupportedCurrencies,
			'Partially Supported' => $supportedCurrencies,
		));
		// If not post or form not valid, return
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}
		$post = $this -> getRequest() -> getPost();
		$cat1_id = $post['cat1_id'];
		$post['cat1_id'] = "";
		if (!$form -> isValid($post))
			return;

		// Process
		$table = Engine_Api::_() -> getItemTable('ynauction_product');
		$db = $table -> getAdapter();
		$db -> beginTransaction();

		try
		{
			// Create Auction
			$values = array_merge($form -> getValues(), array('user_id' => $viewer -> getIdentity(), ));
			$product = $table -> createRow();
			$product -> setFromArray($values);
			if ($cat1_id > 0)
				$product -> cat_id = $cat1_id;
			$flag = 0;
			//check price
			if (!is_numeric($values['price']) || $values['price'] < 0)
			{
				$form -> getElement('price') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['starting_bidprice']) || $values['starting_bidprice'] < 0)
			{
				$form -> getElement('starting_bidprice') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['minimum_increment']) || $values['minimum_increment'] < 0)
			{
				$form -> getElement('minimum_increment') -> addError('The minimum increment number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['maximum_increment']) || $values['maximum_increment'] < 0)
			{
				$form -> getElement('maximum_increment') -> addError('The maximum increment number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			//check start time and end time
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start_time = strtotime($values['start_time']);
			$end_time = strtotime($values['end_time']);
			$now = date('Y-m-d H:i:s');
			date_default_timezone_set($oldTz);

			$product -> start_time = date('Y-m-d H:i:s', $start_time);
			$product -> end_time = date('Y-m-d H:i:s', $end_time);
			if ($values['start_time'] < $now)
			{
				$form -> getElement('start_time') -> addError('Start Time should be equal or greater than Current Time!');
				$flag = 1;
			}
			if ($values['start_time'] >= $values['end_time'])
			{
				$form -> getElement('end_time') -> addError('End Time should be greater than Start Time!');
				$flag = 1;
			}
			if ($values['minimum_increment'] > $values['maximum_increment'])
			{
				$form -> getElement('maximum_increment') -> addError('Maximum increment should be greater than minimum increment!');
				$flag = 1;
			}
			//check image
			if (!empty($values['thumbnail']))
			{
				$file = $form -> thumbnail -> getFileName();
				$info = getimagesize($file);
				if ($info[2] > 3 || $info[2] == "")
				{
					$form -> getElement('thumbnail') -> addError('The uploaded file is not supported or is corrupt.');
					$flag = 1;
				}
			}
			if ($values['check'] == 0)
			{
				$form -> getElement('link') -> addError('Please complete this field - it is required. You must agree to the terms of service to continue.');
				$flag = 1;
			}
			if ($flag == 1)
			{
				return false;
			}
			$product -> price = round($product -> price, 2);
			$product -> starting_bidprice = round($product -> starting_bidprice, 2);
			$product -> bid_price = round($product -> starting_bidprice, 2);
			$product -> bid_time = round($product -> bid_time);
			//Set Fee
			$publish_fee = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynauction_product', $viewer, 'publish_fee');
			$featured_fee = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynauction_product', $viewer, 'feature_fee');
			$mtable = Engine_Api::_() -> getDbtable('permissions', 'authorization');
			$psselect = $mtable -> select() -> where("type = 'ynauction_product'") -> where("level_id = ?", $viewer -> level_id) -> where("name = 'publish_fee'");
			$fsselect = $mtable -> select() -> where("type = 'ynauction_product'") -> where("level_id = ?", $viewer -> level_id) -> where("name = 'feature_fee'");
			$mallow_p = $mtable -> fetchRow($psselect);
			$mallow_f = $mtable -> fetchRow($fsselect);

			if (!empty($mallow_p) && $publish_fee == "")
				$publish_fee = $mallow_p['value'];

			if (!empty($mallow_f) && $featured_fee == "")
				$featured_fee = $mallow_f['value'];
			$total_fee = $publish_fee;
			if ($values['featured'] == 1)
			{
				$total_fee = $total_fee + $featured_fee;
			}
			if (!$total_fee)
			{
				$total_fee = 0;
			}
			$product -> total_fee = $total_fee;
			$product -> creation_ip = ip2long($_SERVER['REMOTE_ADDR']);
			$product -> save();
			// Set photo
			if (!empty($values['thumbnail']))
			{
				$product -> setPhoto($form -> thumbnail);
			}
			// Add fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($product);
			$customfieldform -> saveValues();
			// Set privacy
			$auth = Engine_Api::_() -> authorization() -> context;
			$roles = array(
				'owner',
                'member',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'registered',
                'everyone'
			);

			if (empty($values['auth_view']))
			{
				$values['auth_view'] = array("everyone");
			}
			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = array("everyone");
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($product, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($product, $role, 'comment', ($i <= $commentMax));
			}

			// Commit
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		// Redirect

		return $this -> _helper -> redirector -> gotoRoute(array(
			'action' => 'success',
			'auction' => $product -> product_id
		), 'ynauction_general', true);

	}

	public function successAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// Get navigation
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main', array(), 'ynauction_main_manage');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$this -> view -> auction = $auction = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		if ($viewer -> getIdentity() != $auction -> user_id)
		{
			return $this -> _forward('requireauth', 'error', 'core');
		}
		if ($this -> getRequest() -> isPost() && $this -> getRequest() -> getPost('confirm') == true)
		{
			return $this -> _redirect("ynauction/photo/upload/subject/ynauction_product_" . $this -> _getParam('auction'));
		}
	}

	public function subcategoriesAction()
	{
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$cat_id = $this -> getRequest() -> getParam('cat_id');
		$subCategories = Engine_Api::_() -> ynauction() -> getCategories($cat_id);
		$html = '';
		foreach ($subCategories as $subCategorie)
		{
			$html .= '<option value="' . $subCategorie -> category_id . '" label="' . $subCategorie -> title . '" >' . $subCategorie -> title . '</option>';
		}
		echo $html;
		return;
	}

	public function editAction()
	{
		$this -> _helper -> content -> setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		if (!Engine_Api::_() -> ynauction() -> checkBecome($viewer -> getIdentity()))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		$product = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		$category = Engine_Api::_() -> getItem('ynauction_category', $product -> cat_id);
		if (!Engine_Api::_() -> core() -> hasSubject('product'))
		{
			Engine_Api::_() -> core() -> setSubject($product);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($product, $viewer, 'edit') -> isValid())
			return;
		// Prepare form
		$this -> view -> form = $form = new Ynauction_Form_Edit( array('item' => $product));
		$form -> removeElement('check');
		$form -> removeElement('thumbnail');
		if ($product -> display_home == 1)
		{
			$form -> removeElement('featured');
		}
		if ($product -> photo_id > 0)
			if (!$product -> getPhoto($product -> photo_id))
			{
				$product -> addPhoto($product -> photo_id);
			}

		$category = Engine_Api::_() -> getItem('ynauction_category', $product -> cat_id);
		// prepare subcategories
		if ($category)
		{
			if ($category -> parent > 0)
			{
				$subcategories = Engine_Api::_() -> ynauction() -> getCategories($category -> parent);
				$subSelect = $category -> category_id;
			}
			else
			{
				$subcategories = Engine_Api::_() -> ynauction() -> getCategories($category -> category_id);
				$subSelect = $category -> category_id;
			}
		}
		if (count($subcategories) != 0)
		{
			$form -> cat1_id -> addMultiOption(0, "");
			foreach ($subcategories as $subcategory)
			{
				$form -> cat1_id -> addMultiOption($subcategory -> category_id, $subcategory -> title);
			}
			$form -> cat1_id -> setValue($subSelect);
		}
		$this -> view -> album = $album = $product -> getSingletonAlbum();
		$this -> view -> paginator = $paginator = $album -> getCollectiblesPaginator();

		$paginator -> setCurrentPageNumber($this -> _getParam('page'));
		$paginator -> setItemCountPerPage(100);

		foreach ($paginator as $photo)
		{
			$subform = new Ynauction_Form_Photo_Edit( array('elementsBelongTo' => $photo -> getGuid()));
			$subform -> removeElement('title');
			if ($photo -> file_id == $product -> photo_id)
				$subform -> removeElement('delete');
			$subform -> populate($photo -> toArray());
			$form -> addSubForm($subform, $photo -> getGuid());
			$form -> cover -> addMultiOption($photo -> getIdentity(), $photo -> getIdentity());
		}
		$this -> view -> product = $product;
		// Populate form
		$supportedCurrencyIndex = array();
		$fullySupportedCurrencies = array();
		$supportedCurrencies = array();
		$gateways = array();
		$gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
		foreach ($gatewaysTable->fetchAll() as $gateway)
		{
			$gateways[$gateway -> gateway_id] = $gateway -> title;
			$gatewayObject = $gateway -> getGateway();
			$currencies = $gatewayObject -> getSupportedCurrencies();
			if (empty($currencies))
			{
				continue;
			}
			$supportedCurrencyIndex[$gateway -> title] = $currencies;
			if (empty($fullySupportedCurrencies))
			{
				$fullySupportedCurrencies = $currencies;
			}
			else
			{
				$fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
			}
			$supportedCurrencies = array_merge($supportedCurrencies, $currencies);
		}
		$supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);

		$translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
		$fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
		$supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
		$form -> currency_symbol -> setMultiOptions(array(
			'Fully Supported' => $fullySupportedCurrencies,
			'Partially Supported' => $supportedCurrencies,
		));
		$array = $product -> toArray();
		if ($category -> parent > 0)
		{
			$array['cat_id'] = $category -> parent;
		}
		$options = array();
		$options['format'] = 'Y-M-d H:m:s';
		$array['start_time'] = date('Y-m-d H:i:s', strtotime($this -> view -> locale() -> toDateTime($array['start_time'], $options)));
		$array['end_time'] = date('Y-m-d H:i:s', strtotime($this -> view -> locale() -> toDateTime($array['end_time'], $options)));
		$form -> populate($array);
		$auth = Engine_Api::_() -> authorization() -> context;
		$roles = array(
			'owner',
            'member',
            'owner_member',
            'owner_member_member',
            'owner_network',
            'registered',
            'everyone'
		);

		foreach ($roles as $role)
		{
			if ($auth -> isAllowed($product, $role, 'view'))
			{
				$form -> auth_view -> setValue($role);
			}
			if ($auth -> isAllowed($product, $role, 'comment'))
			{
				$form -> auth_comment -> setValue($role);
			}
		}
		// Check post/form
		if (!$this -> getRequest() -> isPost())
		{
			return;
		}

		$post = $this -> getRequest() -> getPost();
		$cat1_id = $post['cat1_id'];
		$post['cat1_id'] = "";
		if (!$form -> isValid($post))
			return;
		// Process
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();

		try
		{
			$values = $form -> getValues();

			$product -> setFromArray($values);
			if ($cat1_id > 0)
				$product -> cat_id = $cat1_id;
			$product -> modified_date = date('Y-m-d H:i:s');
			$flag = 0;
			//check price
			if (!is_numeric($values['price']) || $values['price'] < 0)
			{
				$form -> getElement('price') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['starting_bidprice']) || $values['starting_bidprice'] < 0)
			{
				$form -> getElement('starting_bidprice') -> addError('The price number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['minimum_increment']) || $values['minimum_increment'] < 0)
			{
				$form -> getElement('minimum_increment') -> addError('The minimum increment number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			if (!is_numeric($values['maximum_increment']) || $values['maximum_increment'] < 0)
			{
				$form -> getElement('maximum_increment') -> addError('The maximum increment number is invalid! (Ex: 2000.25)');
				$flag = 1;
			}
			//check start time and end time
			$oldTz = date_default_timezone_get();
			date_default_timezone_set($viewer -> timezone);
			$start_time = strtotime($values['start_time']);
			$end_time = strtotime($values['end_time']);
			$now = date('Y-m-d H:i:s');
			date_default_timezone_set($oldTz);

			$product -> start_time = date('Y-m-d H:i:s', $start_time);
			$product -> end_time = date('Y-m-d H:i:s', $end_time);

			if ($values['start_time'] < $now)
			{
				$form -> getElement('start_time') -> addError('Start Time should be equal or greater than Current Time!');
				$flag = 1;
			}

			if ($values['start_time'] >= $values['end_time'])
			{
				$form -> getElement('end_time') -> addError('End Time should be greater than Start Time!');
				$flag = 1;
			}
			if ($values['minimum_increment'] > $values['maximum_increment'])
			{
				$form -> getElement('maximum_increment') -> addError('Maximum increment should be greater than minimum increment!');
				$flag = 1;
			}
			if ($flag == 1)
			{
				return false;
			}
			$product -> price = round($product -> price, 2);
			$product -> starting_bidprice = round($product -> starting_bidprice, 2);
			$product -> bid_price = round($product -> starting_bidprice, 2);
			$product -> bid_time = round($product -> bid_time);
			$product -> creation_ip = ip2long($_SERVER['REMOTE_ADDR']);
			$product -> save();
			$cover = $values['cover'];
			// Process
			foreach ($paginator as $photo)
			{
				$subform = $form -> getSubForm($photo -> getGuid());
				$subValues = $subform -> getValues();
				$subValues = $subValues[$photo -> getGuid()];
				unset($subValues['photo_id']);

				if (isset($cover) && $cover == $photo -> photo_id)
				{
					$product -> photo_id = $photo -> file_id;
					$product -> save();
				}

				if (isset($subValues['delete']) && $subValues['delete'] == '1')
				{
					if ($product -> photo_id == $photo -> file_id)
					{
						$product -> photo_id = 0;
						$product -> save();
					}
					$photo -> delete();
				}
				else
				{
					$photo -> setFromArray($subValues);
					$photo -> save();
				}
			}
			// Save custom fields
			$customfieldform = $form -> getSubForm('fields');
			$customfieldform -> setItem($product);
			$customfieldform -> saveValues();
			// Auth
			if (empty($values['auth_view']))
			{
				$values['auth_view'] = 'everyone';
			}

			if (empty($values['auth_comment']))
			{
				$values['auth_comment'] = 'everyone';
			}

			$viewMax = array_search($values['auth_view'], $roles);
			$commentMax = array_search($values['auth_comment'], $roles);

			foreach ($roles as $i => $role)
			{
				$auth -> setAllowed($product, $role, 'view', ($i <= $viewMax));
				$auth -> setAllowed($product, $role, 'comment', ($i <= $commentMax));
			}

			// Rebuild privacy
			$actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
			foreach ($actionTable->getActionsByObject($product) as $action)
			{
				$actionTable -> resetActivityBindings($action);
			}
			$db -> commit();

		}
		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		if ($product -> display_home == 0)
			return $this -> _helper -> redirector -> gotoRoute(array(
				'action' => 'display',
				'auction' => $product -> product_id
			), 'ynauction_general', true);
		else
			return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manageauction'), 'ynauction_general', true);
	}

	public function deleteAction()
	{
		$ynauction = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('ynauction'));
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($ynauction, $viewer, 'delete') -> isValid())
			return;
		$this -> view -> product_id = $ynauction -> getIdentity();
		// This is a smoothbox by default
		if (null === $this -> _helper -> ajaxContext -> getCurrentContext())
			$this -> _helper -> layout -> setLayout('default-simple');
		else// Otherwise no layout
			$this -> _helper -> layout -> disableLayout(true);
		if (!$this -> getRequest() -> isPost())
			return;
		$db = Engine_Api::_() -> getDbtable('products', 'ynauction') -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$ynauction -> is_delete = 1;
			$ynauction -> save();
			
			 //delete actions and attachments
			$streamTbl = Engine_Api::_()->getDbTable('stream', 'activity');
            $streamTbl->delete('(`object_id` = '.$ynauction -> getIdentity().' AND `object_type` = "ynauction_product")');
            $activityTbl = Engine_Api::_()->getDbTable('actions', 'activity');
            $activityTbl->delete('(`object_id` = '.$ynauction -> getIdentity().' AND `object_type` = "ynauction_product")');
            $attachmentTbl = Engine_Api::_()->getDbTable('attachments', 'activity');
            $attachmentTbl->delete('(`id` = '.$ynauction -> getIdentity().' AND `type` = "ynauction_product")');
			
			Engine_Api::_() -> getApi('search', 'core') -> unindex($ynauction);
			//notification
			$notify = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.notify', 0);
			if ($notify == 1)
			{
				//send notify
				//Send sell
				$productOwner = $ynauction -> getOwner();
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				if ($ynauction -> user_id != $viewer -> getIdentity())
				{
					$notifyApi -> addNotification($productOwner, $viewer, $ynauction, 'ynauction_deleted_bidded', array('label' => $auction -> title));
				}
				//send users
				$userBids = Engine_Api::_() -> ynauction() -> getUserBid($ynauction -> product_id, $ynauction -> user_id);
				foreach ($userBids as $bid)
				{
					if ($bid -> ynauction_user_id != $viewer -> getIdentity() && $bid -> ynauction_user_id != $productOwner -> getIdentity())
					{
						$userBid = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
						$notifyApi -> addNotification($userBid, $viewer, $ynauction, 'ynauction_deleted_bidded', array('label' => $auction -> title));
					}
				}
			}
			$db -> commit();
			$this -> view -> success = true;
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Delete Auction successfully.'))
			));
		}
		catch (Exception $e)
		{
			$db -> rollback();
			$this -> view -> success = false;
			throw $e;
		}
	}

	public function manageauctionAction()
	{
		$this -> _helper -> content -> setEnabled();
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		if (!Engine_Api::_() -> ynauction() -> checkBecome($viewer -> getIdentity()))
		{
			return $this -> _helper -> requireAuth -> forward();
		}
		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_quick');
		
		$values = $this ->_getAllParams();
		$values['user_id'] = $this -> view -> viewer_id;
		$paginator = Engine_Api::_() -> ynauction() -> getProductsPaginator($values);
		$this -> view -> paginator = $paginator;
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynauction_page;
		$paginator -> setItemCountPerPage($items_per_page);
		if (isset($values['page']))
			$this -> view -> paginator = $paginator -> setCurrentPageNumber($values['page']);
		$this -> view -> canCreate = $this -> _helper -> requireAuth() -> setAuthParams('ynauction_product', null, 'create') -> checkRequire();
		$view = $this -> view;
		$view -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
	}

	public function detailAction()
	{
		$product = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		if (!$this -> _helper -> requireAuth() -> setAuthParams($product, $viewer, 'view') -> isValid())
			return;
		Engine_Api::_() -> core() -> setSubject($product);
		$this -> _helper -> content -> setNoRender() -> setEnabled();
	}

	public function rateAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);

		if (!$this -> _helper -> requireUser() -> isValid())
			return;

		$ynauction_id = (int)$this -> _getParam('auction_id');
		$rates = (int)$this -> _getParam('rates');

		$viewer = Engine_Api::_() -> user() -> getViewer();

		if ($rates == 0 || $ynauction_id == 0)
		{
			return;
		}
		// Check Auction exist
		$product = Engine_Api::_() -> getItem('ynauction_product', $ynauction_id);
		$can_rate = Engine_Api::_() -> ynauction() -> canRate($product, $viewer -> getIdentity());
		// Check user rated
		if (!$can_rate)
		{
			return;
		}
		$rateTable = Engine_Api::_() -> getDbtable('rates', 'ynauction');
		$db = $rateTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$rate = $rateTable -> createRow();
			$rate -> poster_id = $viewer -> getIdentity();
			$rate -> ynauction_id = $ynauction_id;
			$rate -> rate_number = $rates;
			$rate -> save();
			$rates = Engine_Api::_() -> ynauction() -> getAVGrate($ynauction_id);
			$product -> rates = $rates;
			$product -> save();
			// Commit
			$db -> commit();
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
		return $this -> _redirect("auction/detail/auction/" . $ynauction_id . '#rateauction');

	}

	public function bidAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$ynauction_id = (int)$this -> _getParam('product_id');
		$max_bid = $this -> _getParam('max_bid');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$product = Engine_Api::_() -> getItem('ynauction_product', $ynauction_id);
		$flag = true;
		$latestCanBid = (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.latestbid', 0)) && ($product->bider_id != 0)) ? ($viewer->getIdentity() != $product->bider_id) : true;
		if ($latestCanBid && $product -> status == 0 && $product -> stop == 0 && $product -> is_delete == 0 && $product -> display_home == 1 && $flag == true && $product -> approved == 1 && $max_bid >= $product -> bid_price + $product -> minimum_increment)
		{
			$bidTable = Engine_Api::_() -> getDbtable('bids', 'ynauction');
			$db = $bidTable -> getAdapter();
			$db -> beginTransaction();
			try
			{
				$price = $max_bid;
				$bid = $bidTable -> createRow();
				$bid -> ynauction_user_id = $viewer -> getIdentity();
				$bid -> product_id = $ynauction_id;
				$bid -> product_price = $price;
				$bid -> ip = ip2long($_SERVER['REMOTE_ADDR']);
				$bid -> bid_time = date('Y-m-d H:i:s');
				$bid -> save();

				$product -> bid_price = $price;
				$product -> total_bids = $product -> total_bids + 1;
				$product -> bider_id = $viewer -> getIdentity();
				$product -> save();
				//notification
				$notify = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.notify', 0);
				if ($notify)
				{
					//send notify
					//Send sell
					$productOwner = $product -> getOwner();
					$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
					if ($product -> user_id != $viewer -> getIdentity())
					{
						$notifyApi -> addNotification($productOwner, $viewer, $product, 'ynauction_bidded', array('label' => 'Auction'));
					}
					//send users
					if ($notify == 1)
						$userBids = Engine_Api::_() -> ynauction() -> getLatestUserBid($product -> product_id, $product -> user_id, $product -> bid_price, $viewer -> getIdentity());
					else if ($notify == 2)
						$userBids = Engine_Api::_() -> ynauction() -> getUserBid($product -> product_id, $product -> user_id);
					foreach ($userBids as $bid)
					{
						if ($bid -> ynauction_user_id != $viewer -> getIdentity() && $bid -> ynauction_user_id != $productOwner -> getIdentity())
						{
							$userBid = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
							$notifyApi -> addNotification($userBid, $viewer, $product, 'ynauction_bidded_bidded', array('label' => $auction -> title));
						}
					}
				}
				// Commit
				$db -> commit();
			}

			catch( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}
		}
		echo Zend_Json::encode(array('success' => 1));
	}

	public function updateAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		$ynauction_id = (int)$this -> _getParam('product_id');
		//$flagStart = (int) $this->_getParam('flagStart');
		$pro = Engine_Api::_() -> getItem('ynauction_product', $ynauction_id);
		$now = date('Y-m-d H:i:s');
		//$now = $this->_getParam('now');
		$userDeleted = 0;
		if ($pro -> bider_id == -1)
			$userDeleted = 1;
		$bid = Engine_Api::_() -> ynauction() -> getBid($ynauction_id);
		$price = 0;
		$username = "";
		$time = strtotime($pro -> end_time) - time();
		if ($bid)
		{
			$bider = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
			if ($bider -> getIdentity() > 0)
				$username = '<a href=\"' . $bider -> getHref() . '\">' . $bider -> username . '</a>';
		}
		if ($pro -> bid_price < $pro -> starting_bidprice)
			$price = $pro -> starting_bidprice;
		else
			$price = $pro -> bid_price;
		$price = $this -> view -> locale() -> toCurrency($price, $pro -> currency_symbol);
		$his = "";
		$lasts = Engine_Api::_() -> ynauction() -> getBidHis($pro -> product_id, 10);
		foreach ($lasts as $last)
		{
			$bider = Engine_Api::_() -> getItem('user', $last -> ynauction_user_id);
			$his .= '<div class=\"ynauction_userbid\">' . $bider -> username . '<span class = \"ynauction_datetime\">' . $this -> view -> locale() -> toDateTime($last -> bid_time) . '</span></div>';
		}
		if ($pro -> status == 0 && $pro -> stop == 0 && $pro -> is_delete == 0 && $pro -> display_home == 1 && $product -> approved == 1)
		{
			if ($time > 0)
			{
				$min = floor($time / 60);
				$sec = $time % 60;
				echo '{"min":"' . $min . '", "sec":"' . $sec . '","price":"' . $price . '","username":"' . $username . '","flag":"0","his":"' . $his . '","userDelete":"' . $userDeleted . '"}';
			}
			else
				echo '{"min":"0", "sec":"0","price":"' . $price . '","username":"' . $username . '","flag":"0","his":"' . $his . '","userDelete":"' . $userDeleted . '"}';
		}
		else
		{
			echo '{"min":"0", "sec":"0","price":"' . $price . '","username":"' . $username . '","flag":"0","his":"' . $his . '","remove":"1","userDelete":"' . $userDeleted . '"}';
		}
	}

	public function winAction()
	{
		$this -> _helper -> layout -> disableLayout();
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$ynauction_id = (int)$this -> _getParam('product_id');
		
		$product = Engine_Api::_() -> getItem('ynauction_product', $ynauction_id);
		$winner = Engine_Api::_() -> getItem('user', $product -> bider_id);
		if ($winner -> getIdentity() > 0)
			$username = $winner -> username;
		else $username = '';
		if ($product -> status == 1) {
			echo '{"username":"' . $username . '"}';
			return;
		}
		
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$proTable = Engine_Api::_() -> getDbtable('products', 'ynauction');
		$db = $proTable -> getAdapter();
		$db -> beginTransaction();
		try
		{
			$product = Engine_Api::_() -> getItem('ynauction_product', $ynauction_id);
			$product -> status = 1;
			$product -> display_home = 1;
			$product -> featured = 0;
			$product -> stop = 1;
			$product -> end_time = date('Y-m-d H:i:s');
			$product -> save();
			$winner = Engine_Api::_() -> getItem('user', $product -> bider_id);
			if ($winner -> getIdentity() > 0)
				$username = $winner -> username;
			//send notify
			//Send sell
			$productOwner = $product -> getOwner();
			$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
			if ($product -> user_id != $viewer -> getIdentity())
			{
				$notifyApi -> addNotification($productOwner, $winner, $product, 'ynauction_won', array('label' => 'Auction'));
			}
			//send users
			$userBids = Engine_Api::_() -> ynauction() -> getUserBid($product -> product_id, $product -> user_id);
			foreach ($userBids as $bid)
			{
				if ($bid -> ynauction_user_id != $winner -> getIdentity() && $bid -> ynauction_user_id != $productOwner -> getIdentity())
				{
					$userBid = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
					$notifyApi -> addNotification($userBid, $winner, $product, 'ynauction_won_bidded', array('label' => $product -> title));
				}
			}
			// Commit
			$db -> commit();
			$bid = Engine_Api::_() -> ynauction() -> getBid($ynauction_id);
			echo '{"username":"' . $username . '"}';
		}

		catch( Exception $e )
		{
			$db -> rollBack();
			throw $e;
		}
	}

	public function displayAction()
	{
		$this -> _helper -> content -> setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main', array(), 'ynauction_main_manage');
		$session_id_cart = $this -> getRequest() -> getParam('session_id');
		$product = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		if (!Engine_Api::_() -> core() -> hasSubject('product'))
		{
			Engine_Api::_() -> core() -> setSubject($product);
		}
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		if (!$this -> _helper -> requireAuth() -> setAuthParams($product, $viewer, 'edit') -> isValid())
			return;
		
		$_SESSION['payment_sercurity'] = Ynauction_Api_Cart::getSecurityCode();
		$invoice = Ynauction_Api_Cart::getSecurityCode();
		$this -> view -> product = $product;
		
		
		$account = Engine_Api::_() -> ynauction() ->  getFinanceAccount($viewer -> getIdentity());
		
		if($account){
			$receiver = array(
				'invoice' => $invoice,
				'email' => $account -> account_username,
			);
		}else{
			$message = $this -> view -> translate('There are no account.');
            return $this -> _redirector($message);
		}
		
		
		//******************IMPLEMENT INTERGRATE ADV-PAYMENT*************************
		
        $viewer = Engine_Api::_() -> user() -> getViewer();
       	
		$this -> view -> total_pay = $total_pay = $final_price;
        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');

        if ((!$gatewayTable -> getEnabledGatewayCount() && !Engine_Api::_() -> hasModuleBootstrap('yncredit'))) {
            $message = $this -> view -> translate('There are no payment gateways.');
            return $this -> _redirector($message);
        }
        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynauction');
		
        if ($row = $ordersTable -> getLastPendingOrder()) {
           $row -> delete();
        }
        $db = $ordersTable -> getAdapter();
        $db -> beginTransaction();
        try 
        {
            $ordersTable -> insert(array(
            	'user_id' => $viewer -> getIdentity(), 
	            'creation_date' => new Zend_Db_Expr('NOW()'), 
	            'item_id' => $product -> getIdentity(),
	            'price' => $product -> total_fee, 
	            'currency' => Engine_Api::_() -> getApi('settings', 'core') -> getSetting('payment.currency', 'USD'), 
				'security_code' => $_SESSION['payment_sercurity'],
				'invoice_code' => $invoice,
			));
            // Commit
            $db -> commit();
        } catch (Exception $e) {
            $db -> rollBack();
            throw $e;
        }
		
		// Makebill
		$bill = Ynauction_Api_Cart::makeBillFromCart($product, $receiver, 0);
		
        // Gateways
        $gatewaySelect = $gatewayTable -> select() -> where('enabled = ?', 1);
        $gateways = $gatewayTable -> fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) 
        {
            $gatewayPlugins[] = array('gateway' => $gateway, 'plugin' => $gateway -> getGateway());
        }
        $this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.currency', 'USD');
        $this -> view -> gateways = $gatewayPlugins;
		
		//******************END IMPLEMENT INTERGRATE ADV-PAYMENT*************************
		
	}
	
	public function updateOrderAction() 
    {
        $gateway_id = $this -> _getParam('gateway_id', 0);
        if (!$gateway_id) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $gatewayTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable -> select() -> where('gateway_id = ?', $gateway_id) -> where('enabled = ?', 1);
        $gateway = $gatewayTable -> fetchRow($gatewaySelect);
        if (!$gateway) {
            $message = $this -> view -> translate('Invalid gateway.');
            return $this -> _redirector($message);
        }

        $ordersTable = Engine_Api::_() -> getDbTable('orders', 'ynauction');
        $order = $ordersTable -> getLastPendingOrder();
        if (!$order) {
            $message = $this -> view -> translate('Can not find order.');
            return $this -> _redirector($message);
        }
        $order -> gateway_id = $gateway -> getIdentity();
        $order -> save();

        $this -> view -> status = true;
        if (!in_array($gateway -> title, array('2Checkout', 'PayPal'))) {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process-advanced', 'order_id' => $order -> getIdentity(), 'm' => 'ynauction', 'cancel_route' => 'ynauction_transaction', 'return_route' => 'ynauction_transaction', ), 'ynpayment_paypackage', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        } else {
            $this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'process', 'order_id' => $order -> getIdentity(), ), 'ynauction_transaction', true), 'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Please wait...'))));
        }
    }
	
	public function buynowAction()
	{
		$viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		$product = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> view -> notView = false;
		if ($product -> status == 3)
			$this -> view -> notView = true;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main');
		$session_id_cart = $this -> getRequest() -> getParam('session_id');
		if (!Engine_Api::_() -> core() -> hasSubject('product'))
		{
			Engine_Api::_() -> core() -> setSubject($product);
		}
		// Check auth
		if (!$this -> _helper -> requireSubject() -> isValid())
		{
			return;
		}
		$_SESSION['payment_sercurity'] = Ynauction_Api_Cart::getSecurityCode();
		$method_payment = array(
			'direct' => 'Directly',
			'multi' => 'Multipartite payment'
		);
		$paymentForm = '';
		$gateway_name = "paypal";
		$gateway = Ynauction_Api_Cart::loadGateWay($gateway_name);
		$settings = Ynauction_Api_Cart::getSettingsGateWay($gateway_name);
		$params = array();
		$params = array_merge(array(
			'req3' => 'cancel',
			'req4' => $_SESSION['payment_sercurity']
		), $params);
		$cancelUrl = Zend_Controller_Front::getInstance() -> getRouter() -> assemble($params, 'ynauction_winning', true);
		$_SESSION['url']['cancel'] = $cancelUrl;
		$returnUrl = $this -> selfURL() . 'application/modules/Ynauction/externals/scripts/redirect3.php?pstatus=success&req4=' . $_SESSION['payment_sercurity'] . '&auction=' . $product -> product_id . '&req5=';
		$cancelUrl = $this -> selfURL() . 'application/modules/Ynauction/externals/scripts/redirect3.php?pstatus=cancel&req4=' . $_SESSION['payment_sercurity'] . '&auction=' . $product -> product_id . '&req5=';
		$notifyUrl = $this -> selfURL() . 'application/modules/Ynauction/externals/scripts/callback.php?action=callback&req4=' . $_SESSION['payment_sercurity'] . '&req5=';
		list($receiver, $paramsPay) = Ynauction_Api_Cart::getParamsPay($gateway_name, $returnUrl, $cancelUrl, $method_payment, $notifyUrl);
		$account = Ynauction_Api_Cart::getFinanceAccount($product -> user_id, 2);
		//Send money for Product Owner.
		if ($account)
			$receiver[0]['email'] = $account['account_username'];
		$_SESSION['receiver'] = $receiver;
		$method_payment = 'directly';
		$paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		if ($settings['env'] == 'sandbox')
		{
			$paymentForm = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		}
		else
		{
			$paymentForm = "https://www.paypal.com/cgi-bin/webscr";
		}
		$this -> view -> paymentForm = $paymentForm;
		$this -> view -> method = $method_payment;
		$this -> view -> sercurity = $_SESSION['payment_sercurity'];
		$this -> view -> currency = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.currency', 'USD');
		$this -> view -> paramPay = $paramsPay;
		$this -> view -> receiver = $receiver[0];
		$this -> view -> product = $product;
	}

	public function makebillbuynowAction()
	{
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$receiver = $_SESSION['receiver'];
		$product = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		$viewer_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if (!Engine_Api::_() -> ynauction() -> checkBoughtPrice($product -> product_id, $product -> price))
		{
			$table = Engine_Api::_() -> getItemTable('ynauction_proposal');
			$proposal = $table -> createRow();
			$proposal -> product_id = $product -> product_id;
			$proposal -> ynauction_user_id = $viewer_id;
			$proposal -> proposal_price = $product -> price;
			$proposal -> proposal_time = date('Y-m-d H:i:s');
			$proposal -> approved = 1;
			$proposal -> type = 1;
			$proposal -> save();
		}
		$bill = Ynauction_Api_Cart::makeBillFromCart($product, $receiver[0], 3);
	}

	public function makebillAction()
	{
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$receiver = $_SESSION['receiver'];
		$product = Engine_Api::_() -> getItem('ynauction_product', $this -> _getParam('auction'));
		$bill = Ynauction_Api_Cart::makeBillFromCart($product, $receiver[0], 0);
	}

	public function selfURL()
	{
		$server_array = explode("/", $_SERVER['PHP_SELF']);
		$server_array_mod = array_pop($server_array);
		if ($server_array[count($server_array) - 1] == "admin")
		{
			$server_array_mod = array_pop($server_array);
		}
		$server_info = implode("/", $server_array);
		return "http://" . $_SERVER['HTTP_HOST'] . $server_info . "/";
	}

	public function stopAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$pro_id = $this -> _getParam('product_id');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$ynauction = Engine_Api::_() -> getItem('ynauction_product', $pro_id);
		if (!$this -> _helper -> requireAuth() -> setAuthParams($ynauction, $viewer, 'edit') -> isValid())
			return;
		if ($ynauction)
		{
			$ynauction -> stop = 1;
			$ynauction -> save();
			//notification
			$notify = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('ynauction.notify', 0);
			if ($notify == 1)
			{
				//send notify
				//Send sell
				$productOwner = $ynauction -> getOwner();
				$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
				if ($ynauction -> user_id != $viewer -> getIdentity())
				{
					$notifyApi -> addNotification($productOwner, $viewer, $ynauction, 'ynauction_stopped_bidded', array('label' => $auction -> title));
				}
				//send users
				$userBids = Engine_Api::_() -> ynauction() -> getUserBid($ynauction -> product_id, $ynauction -> user_id);
				foreach ($userBids as $bid)
				{
					if ($bid -> ynauction_user_id != $viewer -> getIdentity() && $bid -> ynauction_user_id != $productOwner -> getIdentity())
					{
						$userBid = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
						$notifyApi -> addNotification($userBid, $viewer, $ynauction, 'ynauction_stopped_bidded', array('label' => $auction -> title));
					}
				}
			}
			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => true,
				'format' => 'smoothbox',
				'messages' => array($this -> view -> translate('Stop Auction successfully.'))
			));
		}
	}

	public function startAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$pro_id = $this -> _getParam('product_id');
		$viewer = $this -> _helper -> api() -> user() -> getViewer();
		$ynauction = Engine_Api::_() -> getItem('ynauction_product', $pro_id);
		if (!$this -> _helper -> requireAuth() -> setAuthParams($ynauction, $viewer, 'edit') -> isValid())
			return;
		if ($ynauction)
		{
			$ynauction -> stop = 0;
			$ynauction -> save();
		}
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Start Auction successfully.'))
		));
	}

	public function viewTransactionAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		$user_id = $this -> getRequest() -> getParam('id');
		$user_name = $this -> getRequest() -> getParam('username');
		$this -> view -> user_name = $user_name;
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$params = array_merge($this -> _paginate_params, array('user_id' => $user_id));
		$this -> view -> history = $his = Ynauction_Api_Cart::getTrackingTransaction($params);
		$his -> setItemCountPerPage(1000000000000);
	}

	public function becomeAction()
	{
		$this -> _helper -> content -> setEnabled();
		// only members can create account
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main', array(), 'ynauction_main_become');
		$this -> view -> form = new Ynauction_Form_BecomeSeller();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> user_id = $viewer -> getIdentity();
		$become = Engine_Api::_() -> ynauction() -> getBecome($viewer -> getIdentity());
		$this -> view -> become = $become;
		if ($this -> getRequest() -> isPost() && $this -> view -> form -> isValid($this -> getRequest() -> getPost()))
		{
			try
			{
				$result = $this -> view -> form -> saveValues();
				if ($result)
					return $this -> _redirect('auction/become');
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
	}

	public function confirmAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$user_id = $this -> getRequest() -> getParam('user_id');
		$auction_id = $this -> getRequest() -> getParam('auction_id');
		$this -> view -> user_id = $user_id;
		$this -> view -> auction_id = $auction_id;
	}

	public function checkConfirmAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$user_id = $this -> getRequest() -> getParam('user_id');
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		//khong su dung view
		$this -> _helper -> viewRenderer -> setNoRender(TRUE);
		if (!Engine_Api::_() -> ynauction() -> checkConfirm($user_id))
		{
			$table = Engine_Api::_() -> getItemTable('ynauction_confirm');
			$db = $table -> getAdapter();
			$db -> beginTransaction();
			$confirm = $table -> createRow();
			$confirm -> user_id = $user_id;
			$confirm -> save();
			$db -> commit();
		}
		echo Zend_Json::encode(array('success' => 1));
	}

	public function historyAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		//tat di layout
		$this -> _helper -> layout -> disableLayout();
		$ynauction = $this -> getRequest() -> getParam('product_id');
		$this -> view -> history = Engine_Api::_() -> ynauction() -> getBidHis($ynauction);
	}

	public function transactionAction()
	{
		$this -> _helper -> content -> setEnabled();
		$user_id = Engine_Api::_() -> user() -> getViewer() -> getIdentity();
		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main', array(), 'ynauction_main_account');
		$params = array_merge($this -> _paginate_params, array(
			'user_id' => $user_id,
			'buy' => 'buy'
		));
		if ($this -> getRequest() -> isPost())
		{
			$values = $this -> getRequest() -> getPost();
			foreach ($values as $key => $value)
			{
				$arr = split("-", $key);
				if ($key == 'item_' . $value)
				{
					$tran = Engine_Api::_() -> getItem('ynauction_transaction_tracking', $value);
					$tran -> approved = 1;
					$tran -> save();
				}
			}
		}
		$this -> view -> history = Ynauction_Api_Cart::getTrackingTransaction($params);
	}

	public function participateAction()
	{
		$this -> _helper -> content -> setEnabled();
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		// Get quick navigation
		$this -> view -> quickNavigation = $quickNavigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_quick');
		$this -> view -> form = $form = new Ynauction_Form_Search();
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> viewer_id = $viewer -> getIdentity();
		
		$values = $this ->_getAllParams();
		$values['participate'] = $this -> view -> viewer_id;
		if ($this -> getRequest() -> getParam('user_id'))
		{
			$values['participate'] = $this -> getRequest() -> getParam('user_id');
		}
		$now = date('Y-m-d H:i:s');
		$values['where'] = "display_home = 1 AND stop = 0 AND end_time >=  '$now' AND approved = '1' AND start_time <= '$now' ";
		$paginator = Engine_Api::_() -> ynauction() -> getProductsPaginator($values);
		$this -> view -> paginator = $paginator;
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynauction_page;
		$paginator->setItemCountPerPage($items_per_page);
		if (isset($values['page']))
			$this -> view -> paginator = $paginator -> setCurrentPageNumber($values['page']);
	}

	public function userAuctionAction()
	{
		// Get navigation
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynauction_main', array(), '');
		$this -> view -> form = $form = new Ynauction_Form_Search();
		$form -> status -> isValid(' ', 'All');
		// Populate form
		$this -> view -> categories = $categories = Engine_Api::_() -> ynauction() -> getCategories(0);
		foreach ($categories as $category)
		{
			$form -> category -> addMultiOption($category -> category_id, $category -> title);
		}

		$post = $this -> getRequest() -> getPost();
		if ($post)
		{
			if ($post['category'] > 0)
			{
				if ($post['subcategory'] > 0)
				{
					$category = Engine_Api::_() -> getItem('ynauction_category', $post['subcategory']);
					if ($category -> parent != $post['category'])
						$post['subcategory'] = 0;
				}
				$this -> view -> subcategories = $subcategories = Engine_Api::_() -> ynauction() -> getCategories($post['category']);

				foreach ($subcategories as $subcategory)
				{
					$form -> subcategory -> addMultiOption($subcategory -> category_id, $subcategory -> title);
				}
			}
			else
				$post['subcategory'] = 0;
			// Process form
			$form -> isValid($post);
			$values = $form -> getValues();
			$this -> view -> search = true;
		}
		$values['user_id'] = $this -> view -> viewer_id;
		if ($this -> getRequest() -> getParam('user_id'))
		{
			$values['user_id'] = $this -> getRequest() -> getParam('user_id');
		}
		$paginator = Engine_Api::_() -> ynauction() -> getProductsPaginator($values);
		$this -> view -> paginator = $paginator;
		$items_per_page = Engine_Api::_() -> getApi('settings', 'core') -> ynauction_page;
		$paginator -> setItemCountPerPage($items_per_page);
		if (isset($values['page']))
			$this -> view -> paginator = $paginator -> setCurrentPageNumber($values['page']);
	}

	public function approveAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$pro_id = $this -> _getParam('auction');
		$ynauction = Engine_Api::_() -> getItem('ynauction_product', $pro_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($ynauction)
		{
			$ynauction -> approved = 1;
			$action = @Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($ynauction -> getOwner(), $ynauction, 'ynauction_new');
			if ($action != null)
			{
				Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $ynauction);
			}
			$ynauction -> save();
		}
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Approve auction successfully.'))
		));
	}

	public function denyAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$pro_id = $this -> _getParam('auction');
		$ynauction = Engine_Api::_() -> getItem('ynauction_product', $pro_id);
		if ($ynauction)
		{
			$ynauction -> approved = -1;
			$ynauction -> save();
		}
		$this -> _forward('success', 'utility', 'core', array(
			'smoothboxClose' => true,
			'parentRefresh' => true,
			'format' => 'smoothbox',
			'messages' => array($this -> view -> translate('Deny auction successfully.'))
		));
	}

	public function publishAction()
	{
		if (!$this -> _helper -> requireUser() -> isValid())
			return;
		$pro_id = $this -> _getParam('auction');
		$ynauction = Engine_Api::_() -> getItem('ynauction_product', $pro_id);
		$viewer = Engine_Api::_() -> user() -> getViewer();
		if ($ynauction)
		{
			$ynauction -> display_home = 1;
			$autoApprove = Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('ynauction_product', $viewer, 'auto_approve');
			if ($autoApprove)
			{
				$ynauction -> approved = 1;
				$action = Engine_Api::_() -> getDbtable('actions', 'activity') -> addActivity($viewer, $ynauction, 'ynauction_new');
				if ($action != null)
				{
					Engine_Api::_() -> getDbtable('actions', 'activity') -> attachActivity($action, $ynauction);
				}
			}
			$ynauction -> save();
		}
		// Redirect

		return $this -> _helper -> redirector -> gotoRoute(array('action' => 'manageauction'), 'ynauction_general', true);
	}

	public function termServiceAction()
	{

		if (!$this -> _helper -> requireUser() -> isValid())
		{
			return;
		}
		/*$affiliate = new Ynauction_Plugin_Menus;
		 if (!$affiliate->canView()) {
		 $this->_redirect('/auction/');
		 //return $this->_helper->redirector->gotoRoute(array(), 'default', true);
		 }  */
		$table = Engine_Api::_() -> getDbTable('statics', 'ynauction');
		$select = $table -> select();
		$select -> where('static_name = ?', 'terms');
		$row = $table -> fetchRow($select);
		if (!count($row))
		{
			return;
		}
		//echo($row[0]->static_content);
		$this -> view -> terms = $row -> static_content;
	}

	protected function _redirector($message = null) {
		if(empty($message))
		{
			$message = Zend_Registry::get('Zend_Translate') -> _('Error!');
		}
		$this -> _forward('success', 'utility', 'core', array('parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array(), 'ynauction_general', true), 'messages' => array($message)));
	}

}
