<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Store/externals/scripts/page.js');
?>

<script type="text/javascript">
  store_page.page_id = '<?php echo $this->page->getIdentity(); ?>';
  store_page.url = '<?php echo $this->url(array('controller' => 'page'), 'store_extended', true); ?>';
  store_page.container_id = 'store_page_container';
  paging.widget_url = '<?php echo $this->url(array('controller' => 'page'), 'store_extended', true); ?>';
	paging.page_id = '<?php echo $this->page->getIdentity(); ?>';
</script>

<?php echo $this->render('_page_list.tpl'); ?>