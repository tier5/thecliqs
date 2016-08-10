<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2013-01-21 16:48:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Widget_ProfileAlbumsController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
      // Don't render this if not authorized
      $viewer = Engine_Api::_()->user()->getViewer();
      if( !Engine_Api::_()->core()->hasSubject() ) {
        return $this->setNoRender();
      }

      // Get subject and check auth
      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
      if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
        return $this->setNoRender();
      }

      $params = array('owner' => $subject);
      $this->view->albums_count = Engine_Api::_()->getDbTable('albums', 'headvancedalbum')->getAlbumPaginator($params)->getTotalItemCount();
      $this->view->photos_count = Engine_Api::_()->getDbTable('photos', 'headvancedalbum')->getPhotoPaginator($params)->getTotalItemCount();
      $params['type'] = 'tagged';
      $this->view->tagged_count = Engine_Api::_()->getDbTable('photos', 'headvancedalbum')->getPhotoPaginator($params)->getTotalItemCount();




    }
}
