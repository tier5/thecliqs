<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: manage-audio.tpl  22.02.12 18:20 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile( $this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
		->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/standalone/audio-player.js');
?>

<script type="text/javascript">
  AudioPlayer.setup("<?php echo $this->layout()->staticBaseUrl?>application/modules/Hegift/externals/standalone/player.swf", {
		width: 290,
		initialvolume: 100,
		transparentpagebg: "yes",
		left: "c49c86",
		lefticon: "c49c86"
	});
</script>

<h2>
  <?php echo $this->translate('Virtual Gifts Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("HEGIFT_VIEWS_SCRIPTS_ADMININDEX_MANAGEAUDIO_DESCRIPTION") ?>
</p>

<br />

<?php echo $this->render('_adminGiftOptions.tpl')?>

<div class="admin_home_middle">

  <?php if ($this->exist) : ?>
    <div class="gift-audio-view" style="position: relative;">
      <div class="gift-audio-view-songs">
        <div id="song_wrapper_<?php echo $this->gift->getIdentity(); ?>">
          <div id="song_<?php echo $this->gift->getIdentity(); ?>"></div>
        </div>
        <script type="text/javascript">
          AudioPlayer.embed("song_<?php echo $this->gift->getIdentity(); ?>", {soundFile: "<?php echo $this->storage->get($this->gift->file_id)->map(); ?>", titles: "<?php echo $this->gift->getTitle(); ?>"});
        </script>
      </div>
    </div>
  <?php endif; ?>

  <div class="settings">
    <?php echo $this->form->render($this)?>
  </div>
</div>