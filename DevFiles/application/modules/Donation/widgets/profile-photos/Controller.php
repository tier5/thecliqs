<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       02.08.12
 * @time       13:36
 */
class Donation_Widget_ProfilePhotosController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;

    public function indexAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        if( !Engine_Api::_()->core()->hasSubject() ) {
            return $this->setNoRender();
        }

        // Get subject and check auth
        $this->view->donation = $subject = Engine_Api::_()->core()->getSubject();

        // Get paginator
        $album = $subject->getSingletonAlbum();
        $this->view->paginator = $paginator = $album->getCollectiblesPaginator();

        // Set item count per page and current page number
        $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 200));
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));


        // Add count to title if configured
        if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
            $this->_childCount = $paginator->getTotalItemCount();
        }
    }

    public function getChildCount()
    {
        return $this->_childCount;
    }

}
