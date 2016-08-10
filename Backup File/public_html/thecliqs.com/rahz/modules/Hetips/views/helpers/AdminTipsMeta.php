<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminTipsMeta.php 2012-03-31 13:34 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_View_Helper_AdminTipsMeta extends Zend_View_Helper_Abstract
{
  public function adminTipsMeta($tip)
  {
    $tipContent = <<<EOF
    <li id="tip_id_{$tip->tip_id}">
      <span class="tip_meta" id="{$tip->id}">
        <div class="tip_delete"><a href="javascript:void(0)">x</a></div>
        <div class="tip_label">{$tip->label}</div>
      </span>
    <li>
EOF;

    return $tipContent;
  }
}