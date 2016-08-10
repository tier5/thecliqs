<?php
class Ynlistings_Widget_ListingReviewsController extends Engine_Content_Widget_Abstract {
    protected $_childCount;
  
    public function indexAction() {
        // Don't render this if not authorized
        $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
        $timezone = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('core_locale_timezone', 'GMT');
        if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
            $timezone = $viewer->timezone;
        }
        $this->view->timezone = $timezone;
        if( !Engine_Api::_()->core()->hasSubject('ynlistings_listing')) {
        return $this->setNoRender();
        }
    
        // Get subject
        $this->view->listing = $listing = Engine_Api::_()->core()->getSubject('ynlistings_listing');
        
        // Do not render if nothing to show and not viewer
        if(!$viewer->getIdentity()) {
            return $this->setNoRender();
        }
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $table = Engine_Api::_()->getItemTable('ynlistings_review');
        $select = $table->select();
        $select
            ->where('listing_id = ?', $listing->getIdentity())
            ->where('user_id <> '.$viewer->getIdentity())
            ->where('user_id <> '.$listing->getOwner()->getIdentity())
            ->order('modified_date');
        $this->view->paginator = $paginator = Zend_Paginator::factory($table->fetchAll($select));
        
        // Set item count per page and current page number
        $paginator->setItemCountPerPage($request->getParam('itemCountPerPage', 5));
        $paginator->setCurrentPageNumber($request->getParam('page', 1));
        $this->view->page = $paginator->getCurrentPageNumber();
        // Add count to title if configured
        if( $this->_getParam('titleCount', true) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
        
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        $this->view->can_rate = $can_rate = $permissionsTable->getAllowed('ynlistings_listing', $viewer->level_id, 'rate');
        
        if (!$listing->isOwner($viewer)) {
            $select->reset();
            $select
                ->where('listing_id = ?', $listing->getIdentity())
                ->where('user_id = ?', $viewer->getIdentity());  
            $this->view->my_review = $my_review = $table->fetchRow($select);
            if ($my_review) {
                $this->_childCount++;
            }
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }
}