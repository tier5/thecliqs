<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  05.04.12 12:02 TeaJay $
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
  var playing_id = 0;
  var playing_file = 0;

  AudioPlayer.setup("<?php echo $this->layout()->staticBaseUrl?>application/modules/Hegift/externals/standalone/player.swf", {
		width: 100,
		transparentpagebg: "yes"
	});

  var play = function(id, file) {
    if ($('gift_audio_'+id).hasClass('icon_gift_audio_pause')) {
      $('gift_audio_'+playing_id).removeClass('icon_gift_audio_pause');
      $('gift_audio_'+playing_id).addClass('icon_gift_audio_play');
      AudioPlayer.embed("song_"+playing_id, {soundFile: playing_file, autostart: 'no', loop: 'no'});
    } else if($('gift_audio_'+id).hasClass('icon_gift_audio_play')) {

      if (playing_id) {
        if ($('gift_audio_'+playing_id).hasClass('icon_gift_audio_pause')) {
          $('gift_audio_'+playing_id).removeClass('icon_gift_audio_pause');
          $('gift_audio_'+playing_id).addClass('icon_gift_audio_play');
          AudioPlayer.embed("song_"+playing_id, {soundFile: playing_file, autostart: 'no'});
        }
      }

      $('gift_audio_'+id).removeClass('icon_gift_audio_play');
      $('gift_audio_'+id).addClass('icon_gift_audio_pause');
      AudioPlayer.embed("song_"+id, {soundFile: file, autostart: 'yes', loop: 'yes'});
      playing_id = id;
      playing_file = file;

      $('song_wrapper_'+id).setStyle('visibility', 'visible');
      setTimeout(function(){
        $('song_wrapper_'+id).setStyle('visibility', 'hidden');
      }, 100);
    }
  }

  var sendGift = function(id) {
    gift_manager.send_url = '<?php echo $this->url(array('action' => 'send'), 'hegift_general', true) ?>';
    gift_manager.open_form(id);
  }

  var play_video = function(id) {
    var url = '<?php echo $this->url(array('action' => 'video-preview'), 'hegift_general', true);?>'+'/gift_id/'+id;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {height: 350, width: 450});
  }

  en4.core.runonce.add(function () {
    gift_manager.action_url = en4.core.baseUrl + 'widget/index/content_id/' + '<?php echo sprintf('%d', $this->identity);?>/user_id/' + '<?php echo $this->subject()->getIdentity()?>';
    gift_manager.initTips();
  });

</script>

<?php
/**
 * @var $rs Hegift_Model_Recipient
 * @var $gift Hegift_Model_Gift
 */
?>

<div class="layout_middle sent_received_layout">
  <div>
    <a id="gift_loader_browse" class="gift_loader_rs hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Hegift/externals/images/loader.gif', ''); ?></a>
  </div>
  <ul class='manage_gifts_list' id="browse_gifts" style="overflow: hidden;">
    <?php foreach( $this->paginator as $rs ):
        $user = $rs->getUser('received');
        $gift = $rs->getGift();
      ?>
      <li class="manage_gift_list_item" id="recipient_<?php echo $rs->getIdentity()?>">
        <div class="item_info hidden">
          <?php if ($rs->getPrivacyForUser($this->viewer)) : ?>
            <div class="manage_gift_tips_details">
              <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('style' => 'float: left')) ?>
              <div class="manage_gift_details">
                <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                <div class="sent_status"><?php echo $this->translate('HEGIFT_sent you this gift ') .'<b>'. $rs->getPrivacy().'</b>' ?></div>
                <div class="gift_manage_info_date"><?php echo $this->translate('HEGIFT_Sent %s ', $this->timestamp($rs->send_date)) ?></div>
              </div>
            </div>
            <?php if ($rs->getMessage()) : ?>
              <div class="clr"></div>
              <div class="manage_gift_message">
                <?php echo $rs->getMessage(); ?>
              </div>
            <?php endif; ?>
          <?php else : ?>
            <div class="sent_status"><?php echo $rs->getPrivacy()?></div>
          <?php endif; ?>
        </div>
        <div class="expWrapper" align="center">
          <div class="gift_manage_info_photo">
            <?php if ($gift->type == 2 && $rs->getPrivacyForUser($this->viewer)) : ?>
              <span class="gift-type-icon icon_gift_audio_play gift_type_position" id="gift_audio_<?php echo $gift->getIdentity()?>" onclick="play('<?php echo $gift->getIdentity()?>', '<?php echo $this->storage->get($gift->file_id)->map(); ?>')" title="<?php echo $this->translate('Play')?>"></span>
              <div id="song_wrapper_<?php echo $gift->getIdentity(); ?>" class="song_wrapper_gift">
                <div id="song_<?php echo $gift->getIdentity(); ?>"></div>
              </div>
            <?php elseif ($gift->type == 3 && $rs->getPrivacyForUser($this->viewer)) : ?>
              <span class="gift-type-icon icon_gift_video_play gift_type_position" title="<?php echo $this->translate('HEGIFT_Preview')?>" onclick="play_video('<?php echo $gift->getIdentity()?>')"></span>
            <?php endif; ?>
            <img class="item_photo_gift" alt="" src="<?php echo $gift->getPhotoUrl('thumb.normal')?>">
          </div>
          <div class="manage_gift_options">
            <ul>
              <?php if ($rs->approved) : ?>
                <?php if ($gift->isGeneral()) : ?>
                  <li>
                    <a class="buttonlink item_icon_gift"
                       onclick="sendGift('<?php echo $gift->getIdentity()?>')"
                       title="<?php echo $this->translate('HEGIFT_Send Gift')?>"
                       href="javascript:void(0)"></a>
                  </li>
                <?php else : ?>
                  <li>
                    <a class="buttonlink"></a>
                  </li>
                <?php endif; ?>
              <?php endif; ?>
            </ul>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
    <?php if( $this->paginator->count() > 1 ): ?>
      <br />
      <?php echo $this->paginationControl($this->paginator, null, array("pagination/gifts.tpl","hegift")); ?>
    <?php endif; ?>
  </ul>
</div>