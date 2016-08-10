<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-08-16 16:48 nurmat $
 * @author     Nurmat
 */

?>
<script type="text/javascript">
  en4.core.runonce.add(function()
  {
    $(document.body).getElement('.layout_daylogo_day_logo').addClass('layout_core_menu_logo');
  });
</script>
<?php

$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('DAYLOGO_Site_Title'));
$logo = $this->defaultLogo;

$route = $this->viewer()->getIdentity()
  ? array('route' => 'user_general', 'action' => 'home')
  : array('route' => 'default');
echo ($logo)
  ? $this->htmlLink($route, $this->htmlImage($logo), array('title' => is_array($this->logoInfo) ? $this->logoInfo['title'] : $this->logoInfo))
  : $this->htmlLink($route, $title);