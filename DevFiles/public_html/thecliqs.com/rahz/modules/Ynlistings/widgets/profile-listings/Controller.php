<?php
class Ynlistings_Widget_ProfileListingsController extends Engine_Content_Widget_Abstract {
    protected $_childCount;
  
    public function indexAction() {
        // Don't render this if not authorized
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
        }

        // Get subject and check auth
        $subject = Engine_Api::_()->core()->getSubject();
        if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
            return $this->setNoRender();
        }

        // Just remove the title decorator
        $this->getElement()->removeDecorator('Title');

        // Get paginator
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('ynlistings_listing')->getListingsPaginator(array(
            'order' => 'creation_date',
            'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
        ));

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));

        // Do not render if nothing to show
        if( $paginator->getTotalItemCount() <= 0 ) {
            return $this->setNoRender();
        }

        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }

    public function getChildCount() {
        return $this->_childCount;
    }
}