<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 09.01.12 14:16 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Widget_SendCreditsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $user = Engine_Api::_()->user()->getViewer();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    if (!$user->getIdentity() || $permissionsTable->getAllowed('credit', $user->level_id, 'transfer') === 0) {
      return $this->setNoRender();
    }
    $this->view->form = $form = new Credit_Form_Send();
  }
}