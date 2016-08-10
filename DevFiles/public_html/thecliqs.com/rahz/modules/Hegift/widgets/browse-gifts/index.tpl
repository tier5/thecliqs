<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  07.02.12 12:40 TeaJay $
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
  gift_manager.widget_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
  gift_manager.send_url = '<?php echo $this->url(array('action' => 'send'), 'hegift_general', true) ?>';
  gift_manager.user_id = '<?php echo $this->user_id?>';

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

  var play_video = function(id) {
    var url = '<?php echo $this->url(array('action' => 'video-preview'), 'hegift_general', true);?>'+'/gift_id/'+id;
    var $element = new Element('a', {'href': url, 'class': 'smoothbox'});
    Smoothbox.open($element, {height: 350, width: 450});
  }

</script>

<div class="browse_gifts_container" id="browse_gifts_container">
  <?php if ($this->user_id) : ?>
    <?php $user = $this->item('user', $this->user_id)?>
    <div class="gift_tip">
      <div class="user_photo">
        <?php echo $this->itemPhoto($user, 'thumb.icon')?>
      </div>
      <div>
        <ul class="form-notices">
          <li>
            <?php
              echo $this->translate("You are sending a gift to %s, if you would like to send a gift to other members too, then just close this filter - %s",
                $this->htmlLink(
                  $user->getHref(),
                  $user->getTitle(),
                  array('target' => '_blank')
                ),
                $this->htmlLink('javascript:void(0)', $this->translate('HEGIFT_close'),
                  array('onclick' => 'gift_manager.user_id=0; gift_manager.getGifts()')
                )
              );
            ?>
          </li>
        </ul>
      </div>
      <div class="clr"></div>
    </div>
  <?php endif; ?>
  <div class="layout_core_container_tabs fw_active_theme_<?php echo $this->activeTheme()?>">
    <div class="tabs_alt tabs_parent">
      <ul id="main_tabs">
        <li class="<?php if ($this->sort == 'recent') echo 'active'; ?>">
          <a class="gift_sort_buttons" id="gift_sort_recent" href="javascript:void(0)"
            onclick="gift_manager.setSort('recent');"><?php echo $this->translate("HEGIFT_Recent")?></a>
        </li>
        <li class="<?php if ($this->sort == 'popular') echo 'active'; ?>">
          <a class="gift_sort_buttons" id="gift_sort_popular" href="javascript:void(0)"
            onclick="gift_manager.setSort('popular');"><?php echo $this->translate("HEGIFT_Popular")?></a>
        </li>
        <li class="<?php if ($this->sort == 'actual') echo 'active'; ?>">
          <a class="gift_sort_buttons" id="gift_sort_actual" href="javascript:void(0)"
            onclick="gift_manager.setSort('actual');"><?php echo $this->translate("HEGIFT_Actual")?></a>
        </li>
        <li class="<?php if ($this->sort == 'photo') echo 'active'; ?>">
          <a class="gift_sort_buttons" id="gift_sort_photo" href="javascript:void(0)"
            onclick="gift_manager.setSort('photo');"><?php echo $this->translate("HEGIFT_Photo")?></a>
        </li>
        <li class="<?php if ($this->sort == 'audio') echo 'active'; ?>">
          <a class="gift_sort_buttons" id="gift_sort_audio" href="javascript:void(0)"
            onclick="gift_manager.setSort('audio');"><?php echo $this->translate("HEGIFT_Audio")?></a>
        </li>
        <li class="<?php if ($this->sort == 'video') echo 'active'; ?>">
          <a class="gift_sort_buttons" id="gift_sort_video" href="javascript:void(0)"
            onclick="gift_manager.setSort('video');"><?php echo $this->translate("HEGIFT_Video")?></a>
        </li>
        <a id="gift_loader_browse" class="gift_loader_browse hidden"><?php echo $this->htmlImage($this->layout()->staticBaseUrl.'application/modules/Hegift/externals/images/loader.gif', ''); ?></a>
      </ul>
    </div>
  </div>
  <?php if ($this->count > 0): ?>
    <ul class="browse_gifts" id="browse_gifts" style="overflow: hidden;">
      <?php foreach ($this->gifts as $gift): ?>
        <li class="hegift_widget_list_item">
          <div class="expWrapper" align="center">
            <a class="" style="text-decoration: none" name="<?php echo $gift->credits?>"
              id="gift_<?php echo $gift->getIdentity()?>" href="javascript:void(0)">
              <div class="gift">
                <?php if ($gift->type == 2 && $this->viewer->getIdentity()) : ?>
                  <span class="gift-type-icon icon_gift_audio_play" id="gift_audio_<?php echo $gift->getIdentity()?>" onclick="play('<?php echo $gift->getIdentity()?>', '<?php echo $this->storage->get($gift->file_id)->map(); ?>')" title="<?php echo $this->translate('Play')?>"></span>
                  <div id="song_wrapper_<?php echo $gift->getIdentity(); ?>" class="song_wrapper_gift">
                    <div id="song_<?php echo $gift->getIdentity(); ?>"></div>
                  </div>
                <?php elseif ($gift->type == 3 && $this->viewer->getIdentity()) : ?>
                  <span class="gift-type-icon icon_gift_video_play" title="<?php echo $this->translate('HEGIFT_Preview')?>" onclick="play_video('<?php echo $gift->getIdentity()?>')"></span>
                <?php endif; ?>
                <div title="<?php echo $gift->getTitle()?>" class="gift-type gift-type-<?php echo $gift->getTypeName()?>"><?php echo $this->string()->truncate($gift->getTitle(), 10, '...')?></div>
                <div onclick="gift_manager.open_form('<?php echo $gift->getIdentity()?>')" onfocus="this.blur();">
                  <div class="gift-thumbnail">
                    <img src="<?php echo $gift->getPhotoUrl('thumb.normal'); ?>" style="max-width: 80px; max-height: 90px;">
                  </div>
                  <div class="gift-info">
                    <span class="gift_amount">
                      <?php if ($gift->amount) : ?>
                        <?php echo $this->locale()->toNumber($gift->amount) . ' ' . $this->translate('HEGIFT_left'); ?>
                      <?php endif; ?>
                    </span>
                    <br />
                    <span style="color : #008000;">
                      <?php if ($gift->credits) : ?>
                        <?php echo $this->translate("HEGIFT_%s credit", $this->locale()->toNumber($gift->credits))?>
                      <?php else : ?>
                        <?php echo $this->translate('HEGIFT_Free')?>
                      <?php endif; ?>
                    </span>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php echo $this->paginationControl($this->gifts, null, array("pagination/gifts.tpl","hegift")); ?>
  <?php else : ?>
    <div class="tip"><span><?php echo $this->translate("HEGIFT_There are no gifts."); ?></span></div>
  <?php endif; ?>
</div>

<?php if ($this->gift_id) : ?>
  <script type="text/javascript">
    window.addEvent('load', function() {
      gift_manager.open_form('<?php echo $this->gift_id?>');
    });
  </script>
<?php endif; ?>
