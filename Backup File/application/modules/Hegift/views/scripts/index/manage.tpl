<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: manage.tpl  11.02.12 17:50 TeaJay $
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
  $this->headTranslate(array('HEGIFT_Do_This_Action_Confirm'));
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

  var setOptionUrl = function(url) {
    gift_manager.action_url = '<?php echo $this->url(array('action' => 'manage'), 'hegift_general', true) ?>';
    gift_manager.options_url = url;
    gift_manager.getGiftsByOptions();
  }

  var play_video = function(id) {
    var url = '<?php echo $this->url(array('action' => 'video-preview'), 'hegift_general', true);?>'+'/gift_id/'+id;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {height: 350, width: 450});
  }

  en4.core.runonce.add(function (){
    gift_manager.action_url = '<?php echo $this->url(array('action' => 'manage'), 'hegift_general', true) ?>';
    gift_manager.initTips();
  });

</script>

<div class="headline">
  <h2>
    <?php echo $this->translate('Virtual Gifts');?>
  </h2>
  <?php if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->navigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class="gift_buttons">
  <a id="received_gift_button" class="gift_buttonlink received_gift_button active_gift_button"
     onclick="gift_manager.setActionName('received')">
    <span class="gift_button_text"><?php echo $this->translate('HEGIFT_Received Gifts')?></span>
    (<span id="received_gift_button_count"><?php echo $this->locale()->toNumber($this->received_gifts_count); ?></span>)
  </a>
  <a id="sent_gift_button" class="gift_buttonlink sent_gift_button not_active_sent_gift_button"
     onclick="gift_manager.setActionName('sent')">
    <span class="gift_button_text"><?php echo $this->translate('HEGIFT_Sent Gifts')?></span>
    (<span id="sent_gift_button_count"><?php echo $this->locale()->toNumber($this->sent_gifts_count); ?></span>)
  </a>
</div>

<?php
/**
 * @var $rs Hegift_Model_Recipient
 * @var $gift Hegift_Model_Gift
 */
?>

<div class="layout_middle sent_received_layout">
  <p style="margin: 10px">
    <?php echo $this->translate('HEGIFT_'.strtoupper($this->action_name).'_GIFTS_DESC'); ?>
  </p>
  <div>
    <a id="gift_loader_browse" class="gift_loader_rs hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Hegift/externals/images/loader.gif', ''); ?></a>
  </div>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <ul class='manage_gifts_list' id="browse_gifts" style="overflow: hidden;">
      <?php foreach( $this->paginator as $rs ):
          $user = $rs->getUser($this->action_name);
          $gift = $rs->getGift();
        ?>
        <?php if ($this->action_name == 'received') : ?>
          <li class="manage_gift_list_item <?php if ($this->active_recipient_id == $rs->getIdentity()) echo 'active_gift'?>" id="recipient_<?php echo $rs->getIdentity()?>">
            <div class="item_info hidden">
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
            </div>
            <div class="expWrapper" align="center">
              <div class="gift_manage_info_photo">
                <?php if ($gift->type == 2) : ?>
                  <span class="gift-type-icon icon_gift_audio_play gift_type_position" id="gift_audio_<?php echo $gift->getIdentity()?>" onclick="play('<?php echo $gift->getIdentity()?>', '<?php echo $this->storage->get($gift->file_id)->map(); ?>')" title="<?php echo $this->translate('Play')?>"></span>
                  <div id="song_wrapper_<?php echo $gift->getIdentity(); ?>" class="song_wrapper_gift">
                    <div id="song_<?php echo $gift->getIdentity(); ?>"></div>
                  </div>
                <?php elseif ($gift->type == 3) : ?>
                  <span class="gift-type-icon icon_gift_video_play gift_type_position" title="<?php echo $this->translate('HEGIFT_Preview')?>" onclick="play_video('<?php echo $gift->getIdentity()?>')"></span>
                <?php endif; ?>
                <img class="item_photo_gift" alt="" src="<?php echo $gift->getPhotoUrl('thumb.normal')?>">
              </div>
              <div class="manage_gift_options">
                <ul>
                  <?php if ($rs->approved) : ?>
                    <li>
                      <a class="buttonlink icon_gifts_decline"
                         onclick="setOptionUrl('<?php echo $this->url(array('action'=>'approve', 'recipient_id' => $rs->getIdentity(), 'value' => 0), 'hegift_general', true)?>')"
                         title="<?php echo $this->translate('HEGIFT_Decline')?>"
                         href="javascript:void(0)"></a>
                    </li>
                    <?php if ($this->active_recipient_id != $rs->getIdentity()) : ?>
                      <li>
                        <a class="buttonlink icon_gifts_not_active"
                           onclick="setOptionUrl('<?php echo $this->url(array('action'=>'active', 'recipient_id' => $rs->getIdentity()), 'hegift_general', true)?>')"
                           title="<?php echo $this->translate('HEGIFT_Active Gift')?>"
                           href="javascript:void(0)"></a>
                      </li>
                      <?php if ($gift->isGeneral()) : ?>
                        <li>
                          <a class="buttonlink item_icon_gift"
                             onclick="sendGift('<?php echo $gift->getIdentity()?>')"
                             title="<?php echo $this->translate('HEGIFT_Send Gift')?>"
                             href="javascript:void(0)"></a>
                        </li>
                      <?php endif; ?>
                    <?php else :?>
                      <li>
                        <a class="buttonlink icon_gifts_active"
                           onclick="setOptionUrl('<?php echo $this->url(array('action'=>'active', 'recipient_id' => $rs->getIdentity()), 'hegift_general', true)?>')"
                           title="<?php echo $this->translate('HEGIFT_Disactive')?>"
                           href="javascript:void(0)"></a>
                      </li>
                      <?php if ($gift->isGeneral()) : ?>
                        <li>
                          <a class="buttonlink item_icon_gift"
                             onclick="sendGift('<?php echo $gift->getIdentity()?>')"
                             title="<?php echo $this->translate('HEGIFT_Send Gift')?>"
                             href="javascript:void(0)"></a>
                        </li>
                      <?php endif; ?>
                      <li>
                        <?php echo $this->htmlLink($this->viewer->getHref(), '', array('target' => '_blank', 'class' => 'buttonlink icon_gifts_check', 'title' => $this->translate('HEGIFT_Check here')));?>
                      </li>
                    <?php endif; ?>
                  <?php endif; ?>
                </ul>
              </div>
            </div>
          </li>
        <?php else : ?>
          <li class="manage_gift_list_item" id="sent_<?php echo $rs->getIdentity()?>">
            <div class="item_info" style="display: none">
              <div class="manage_gift_tips_details">
                <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), array('style' => 'float: left')) ?>
                <div class="manage_gift_details">
                  <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                  <div class="sent_status"><?php echo $this->translate('HEGIFT_received from you this gift ') .'<b>'. $rs->getPrivacy().'</b>' ?></div>
                  <div class="gift_manage_info_date"><?php echo $this->translate('HEGIFT_Sent %s ', $this->timestamp($rs->send_date)) ?></div>
                </div>
              </div>
              <?php if ($rs->getMessage()) : ?>
                <div class="clr"></div>
                <div class="manage_gift_message">
                  <?php echo $rs->getMessage(); ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="expWrapper" align="center">
              <div class="gift_manage_info_photo">
                <?php if ($gift->type == 2) : ?>
                  <span class="gift-type-icon icon_gift_audio_play gift_type_position" id="gift_audio_<?php echo $gift->getIdentity()?>" onclick="play('<?php echo $gift->getIdentity()?>', '<?php echo $this->storage->get($gift->file_id)->map(); ?>')" title="<?php echo $this->translate('Play')?>"></span>
                  <div id="song_wrapper_<?php echo $gift->getIdentity(); ?>" class="song_wrapper_gift">
                    <div id="song_<?php echo $gift->getIdentity(); ?>"></div>
                  </div>
                <?php elseif ($gift->type == 3) : ?>
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
        <?php endif; ?>
      <?php endforeach; ?>
      <?php if( $this->paginator->count() > 1 ): ?>
        <br />
        <?php echo $this->paginationControl($this->paginator, null, array("pagination/gifts.tpl","hegift")); ?>
      <?php endif; ?>
    </ul>
  <?php else: ?>
    <div class="tip" style="margin-top: 10px">
      <span>
        <?php echo $this->translate('HEGIFT_You do not have any gifts yet.');?>
      </span>
    </div>
  <?php endif; ?>
</div>
