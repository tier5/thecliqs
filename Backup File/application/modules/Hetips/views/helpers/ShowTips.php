<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ShowTips.php 2012-03-31 13:34 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_View_Helper_ShowTips  extends Zend_View_Helper_Abstract
{

  public function showTips($tips, $type, $settings)
  {
    $content = '';
      if ($settings[$type.'_show_labels']) {
        foreach($tips as $label => $tip){
          if ($tip != null) {
            $tip = Engine_Api::_()->hecore()->truncate($tip, 100);
            $content .= <<<EOL
              <li><span style="font-weight:bold;">{$this->view->translate($label)}:</span> {$this->view->translate($tip)}</li>
EOL;
          }
        }
      } else {
        foreach($tips as $tip){
          if ($tip != null) {
            $tip = Engine_Api::_()->hecore()->truncate($tip, 100);
            $content .= <<<EOL
              <li>{$this->view->translate($tip)}</li>
EOL;
          }
        }
      }
    return $content;
  }
}