<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 05.09.12
 * Time: 15:04
 * To change this template use File | Settings | File Templates.
 */
?>

<?php
$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Donation/externals/scripts/donation.js');
?>

<script type="text/javascript">
  window.addEvent('load',function(){
    setTimeout(function(){
      $$('.headline')[0].setStyle('display','block');
    }, 2000)
  });

  window.addEvent('domready', function(){
    donation.url.manage_donations = "<?php echo $this->url(array('controller' => 'page', 'action' => 'index', 'page_id' =>$this->subject->getIdentity()), 'donation_extended', true); ?>";
    donation.page_id = <?php echo $this->subject->getIdentity(); ?>;
    donation.container_id = 'page_donation_container';
    donation.itemCountPerPage = 3;
    donation.type = 'all';
    donation.init();
  });
</script>

<?php echo $this->render('editMenu.tpl'); ?>

<div class="headline donation">
    <h2><?php echo $this->translate('DONATION_Manage Donations'); ?></h2>
    <div class="tabs">
      <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?>
      <div class="donation_loader hidden" id="donation_loader">
        <?php echo $this->htmlImage($this->baseUrl().'/application/modules/Donation/externals/images/loader.gif'); ?>
      </div>
      <div class="clr"></div>
    </div>
</div>

<p>
  <?php echo $this->translate('DONATION_PAGE_DONATIONS_DESCRIPTION')?>
</p>

<br/>

<?php echo $this->render('donations_list_edit.tpl'); ?>