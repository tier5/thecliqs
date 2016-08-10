<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: audio.tpl  28.02.12 19:40 TeaJay $
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

<?php if (!isset($this->message)) : ?>
  <div class="gift_on_smoothbox">
    <div id="song_wrapper_<?php echo $this->gift->getIdentity(); ?>">
      <div id="song_<?php echo $this->gift->getIdentity(); ?>"></div>
    </div>
    <script type="text/javascript">
      AudioPlayer.embed("song_<?php echo $this->gift->getIdentity(); ?>", {soundFile: "<?php echo $this->audio_location; ?>", titles: "<?php echo $this->gift->getTitle(); ?>", autostart: 'yes', loop: 'yes'});
    </script>
  </div>
<?php else : ?>
  <?php echo $this->translate($this->message);?>
<?php endif; ?>
