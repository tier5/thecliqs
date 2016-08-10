<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  14.02.12 18:31 TeaJay $
 * @author     Taalay
 */
?>

<?php
	$this->headScript()
    ->appendFile( $this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile( $this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/scripts/core.js')
    ->appendFile( $this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/standalone/audio-player.js')
  ;
?>

<script type="text/javascript">

  AudioPlayer.setup("<?php echo $this->layout()->staticBaseUrl?>application/modules/Hegift/externals/standalone/player.swf", {
		width: 100,
		transparentpagebg: "yes"
	});

  var play = function(id, file) {
    if ($('gift_audio_'+id).hasClass('icon_gift_audio_pause')) {
      $('gift_audio_'+id).removeClass('icon_gift_audio_pause');
      $('gift_audio_'+id).addClass('icon_gift_audio_play');
      AudioPlayer.embed("song_"+id, {soundFile: file, autostart: 'no', loop: 'no'});
    } else if($('gift_audio_'+id).hasClass('icon_gift_audio_play')) {
      $('gift_audio_'+id).removeClass('icon_gift_audio_play');
      $('gift_audio_'+id).addClass('icon_gift_audio_pause');
      AudioPlayer.embed("song_"+id, {soundFile: file, autostart: 'yes', loop: 'yes'});

      $('song_wrapper_'+id).setStyle('visibility', 'visible');
      setTimeout(function(){
        $('song_wrapper_'+id).setStyle('visibility', 'hidden');
      }, 100);
    }
  }

  var play_video = function(id, recipeint_id) {
    var url = '<?php echo $this->url(array('action' => 'video'), 'hegift_general', true);?>'+'/gift_id/'+id+'/recipient_id/'+recipeint_id;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {height: 350, width: 450});
  }

  en4.core.runonce.add(function() {
    gift_manager.initProfileTips();
  });
</script>

<div id='profile_photo' style="position: relative;">
  <?php echo $this->itemPhoto($this->subject()) ?>
  <?php if ($this->gift) : ?>
    <div class="received_gift_info gift_photo_position">
      <img src="<?php echo $this->gift->getPhotoUrl('thumb.normal') ?>" alt="">
      <?php if($this->privacy) : ?>
        <div class="item_info hidden">
          <div class="manage_gift_tips_details">
            <?php echo $this->htmlLink($this->from->getHref(), $this->itemPhoto($this->from, 'thumb.icon'), array('style' => 'float: left')) ?>
            <div class="manage_gift_details">
              <?php echo $this->htmlLink($this->from->getHref(), $this->from->getTitle()) ?>
              <div class="sent_status"><?php echo $this->translate('HEGIFT_sent you this gift ') .'<b>'. $this->recipient->getPrivacy().'</b>' ?></div>
              <div class="gift_manage_info_date"><?php echo $this->translate('HEGIFT_Sent %s ', $this->timestamp($this->recipient->send_date)) ?></div>
            </div>
          </div>
          <?php if ($this->recipient->getMessage()) : ?>
            <div class="clr"></div>
            <div class="manage_gift_message">
              <?php echo $this->recipient->getMessage(); ?>
            </div>
          <?php endif; ?>
        </div>
        <?php if ($this->gift->type == 2) : ?>
          <span class="gift-type-icon icon_gift_audio_play custom_gift_position" id="gift_audio_<?php echo $this->gift->getIdentity()?>"
                onclick="play('<?php echo $this->gift->getIdentity()?>', '<?php echo $this->storage->get($this->gift->file_id)->map(); ?>')"
                title="<?php echo $this->translate('HEGIFT_Play')?>">
          </span>
          <div id="song_wrapper_<?php echo $this->gift->getIdentity(); ?>" class="song_wrapper_gift">
            <div id="song_<?php echo $this->gift->getIdentity(); ?>"></div>
          </div>
        <?php elseif ($this->gift->type == 3) : ?>
          <span class="gift-type-icon icon_gift_video_play custom_gift_position" title="<?php echo $this->translate('HEGIFT_Preview')?>"
                onclick="play_video('<?php echo $this->gift->getIdentity()?>', '<?php echo $this->recipient_id?>')">
          </span>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>
