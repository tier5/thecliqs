<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       15.08.12
 * @time       16:46
 */?>

<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Donation/externals/scripts/donation.js');
?>

<script type="text/javascript">
  //<![CDATA[
  en4.core.runonce.add(function(){
    donation.url.browse_charity = "<?php echo $this->url(array('controller' => 'page', 'action' => 'browse','type' => 'charity'),'donation_extended'); ?>";
    donation.url.browse_project = "<?php echo $this->url(array('controller' => 'page', 'action' => 'browse', 'type' => 'project'),'donation_extended'); ?>";
    donation.url.manage_donations = "<?php echo $this->url(array('controller' => 'page', 'action' => 'browse'),'donation_extended'); ?>";

    donation.page_id = <?php echo $this->subject->getIdentity(); ?>;
    donation.container_id = 'page_donation_container';
    donation.itemCountPerPage = <?php echo $this->itemCountPerPage; ?>;
    donation.type = '<?php echo $this->type; ?>';

    donation.init();
    donation.set_active_menu(donation.type);
    <?php echo $this->init_js_str; ?>

  });
  //]]>
</script>

<div id="page_donation_navigation">
  <?php echo $this->render('navigation.tpl'); ?>
</div>
<div id="page_donation_container">
  <?php if($this->type == 'charity'): ?>
    <?php echo $this->render('charity_list.tpl'); ?>
  <?php else: ?>
    <?php echo $this->render('project_list.tpl'); ?>
  <?php endif; ?>
</div>

