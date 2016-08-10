<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-08-16 16:14 nurmat $
 * @author     Nurmat
 */

class Daylogo_Widget_DayLogoController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $table = Engine_Api::_()->getDbTable('logos', 'daylogo');
    $preview_id = false;
    if (isset($_GET['preview_id']) and is_numeric($_GET['preview_id'])) {
      $preview_id = $_GET['preview_id'];
      $this->view->defaultLogo = $table->getPreviewLogo($preview_id);
      $this->view->logoInfo = $table->getLogo($preview_id);
    }
    if (!$preview_id or $this->view->defaultLogo === false) {
      $logo_id = $this->_getParam('logo_id');

      if ($logo_id > 0) {
        $logo = $table->select()
          ->where('logo_id = ?', $logo_id)
          ->query()
          ->fetch();
        $active_logo_id = $table->checkDaylogo($logo['start_date'], $logo['end_date'], $this->_getAllParams());
        $this->view->defaultLogo = $table->getLogoPath('daylogo.day-logo');
        $this->view->logoInfo = is_numeric($active_logo_id) ? $table->getLogo($active_logo_id, 'logo') : '';
      }
      if ($logo_id == 0) {
        $active_logo_id = $table->getDaylogo($this->_getAllParams());
        $this->view->defaultLogo = $table->getLogoPath('daylogo.day-logo');
        $this->view->logoInfo = is_numeric($active_logo_id) ? $table->getLogo($active_logo_id, 'logo') : '';
      }
    }
  }
}