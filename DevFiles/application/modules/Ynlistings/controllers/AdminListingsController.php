<?php
class Ynlistings_AdminListingsController extends Core_Controller_Action_Admin {
    
    public function indexAction() {
        
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynlistings_admin_main', array(), 'ynlistings_admin_main_listings');
        
        $this->view->form = $form = new Ynlistings_Form_Admin_Listings_Search();
        
        $categories = Engine_Api::_() -> getItemTable('ynlistings_category') -> getCategories();
        unset($categories[0]);
        foreach ($categories as $category) {
            $form->category_id->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
        }
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $values['admin'] = 1;
        $this->view->formValues = $values;
 
        $page = $this->_getParam('page', 1);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('ynlistings_listing')->getListingsPaginator($values);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function multiselectedAction() {
        $action = $this -> _getParam('select_action', 'Delete');
        $this->view->action = $action;
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true) {
            $ids_array = explode(",", $ids);
            switch ($action) {
                case 'Delete':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
                        if ($listing && $listing->isDeletable()) {
                            if ($listing->photo_id) {
                                Engine_Api::_()->getItem('storage_file', $listing->photo_id)->remove();
                            }
                            $listing->delete();
                        }
                    }
                    break;
                    
                case 'Approve':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
                        if ($listing) {
                            if ($listing->approved_status == 'pending' && $listing->status == 'open') {
                                
								//save approved date
                                $listing->approved_status = 'approved';
								$listing -> approved_date = date("Y-m-d H:i:s");
								
								//save feature expire date if feature
								if($listing -> featured)
								{
									if($listing->feature_day_number == 1)
									{
										$type = 'day';
									}
									else 
									{
										$type = 'days';
									}
									$now =  date("Y-m-d H:i:s");
									$expiration_date = date_add(date_create($now),date_interval_create_from_date_string($listing->feature_day_number." ".$type));
									$listing -> feature_expiration_date = date_format($expiration_date,"Y-m-d H:i:s");
								}
							    $listing->save();
								
								//send notification to follower
								$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
								$owner = $listing -> getOwner();
								// get follower
								$tableFollow = Engine_Api::_() -> getItemTable('ynlistings_follow');
								$select = $tableFollow -> select() -> where('owner_id = ?', $owner -> getIdentity()) -> where('status = 1');
								$follower = $tableFollow -> fetchAll($select);
								foreach($follower as $row)
								{
									$person = Engine_Api::_()->getItem('user', $row -> user_id);
									$notifyApi -> addNotification($person, $owner, $listing, 'ynlistings_listing_follow');
								}
                                
                                //send notifications end add activity on feed
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                                $notifyApi -> addNotification($owner, $viewer, $listing, 'ynlistings_listing_approve');
                                
                                $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
                                $action = $activityApi->addActivity($owner, $listing, 'ynlistings_listing_create');
                                if($action) {
                                    $activityApi->attachActivity($action, $listing);
                                }
                            }
					    }
                    }
                    break;
                    
                case 'Deny':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
                        if ($listing) {
                            if ($listing->approved_status == 'pending' && $listing->status == 'open') {
                                $listing->approved_status = 'denied';
                                $listing->save();
                                
                                $owner = $listing -> getOwner();
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
                                $notifyApi -> addNotification($owner, $viewer, $listing, 'ynlistings_listing_deny');
                            }
                        }
                    }
                    break;
                
                case 'Feature':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
                        if ($listing) {
                            $listing->featured = 1;
                            $listing->save();
                        }
                    }
                    break;
                    
                case 'Unfeature':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
                        if ($listing) {
                            $listing->featured = 0;
                            $listing->save();
                        }
                    }
                    break;     
            }

            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }

    public function featureAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
        if ($listing) {
            $listing->featured = $value;
			$listing->feature_expiration_date = null;
            $listing->save();
        }
    }
    
    public function highlightAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $listtings = Engine_Api::_()->getItemTable('ynlistings_listing');
        $listtings->update(array('highlight' => 0), array());
        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
        if ($listing) {
            $listing->highlight = true;
            $listing->save();
        }
    }
    
    public function deleteAction() {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->listing_id = $id;
        $listing = Engine_Api::_()->getItem('ynlistings_listing', $id);
        if (!$listing->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to delete this listing.';
            return;
        }
        
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                if ($listing->photo_id) {
                    Engine_Api::_()->getItem('storage_file', $listing->photo_id)->remove();
                }
                $listing->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-listings/delete.tpl');
    }
}