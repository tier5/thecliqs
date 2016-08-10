<?php
/**
 * SocialEngine
 *
 * @category  Application_Extensions
 * @package   Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license   http://www.hire-experts.com
 * @version   $Id: index.tpl 2/9/12 11:10 AM mt.uulu $
 * @author    Mirlan
 */
?>
<?php

$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/page_timeline/page_manager.js')
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/page_timeline/page_listener.js')
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/page_timeline/page_core.js');
?>

<?php
if ($this->hegiftEnabled):
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/flowplayer/flashembed-1.0.1.pack.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hegift/externals/standalone/audio-player.js');
  ?>

<script type="text/javascript">

  <!--  AudioPlayer.setup("--><?php //echo $this->layout()->staticBaseUrl?><!--application/modules/Hegift/externals/standalone/player.swf", {-->
  <!--    width:100,-->
  <!--    transparentpagebg:"yes"-->
  <!--  });-->
  <!---->
  <!--  var play = function (id, file) {-->
  <!--    if ($('gift_audio_' + id).hasClass('icon_gift_audio_pause')) {-->
  <!--      $('gift_audio_' + id).removeClass('icon_gift_audio_pause');-->
  <!--      $('gift_audio_' + id).addClass('icon_gift_audio_play');-->
  <!--      AudioPlayer.embed("song_" + id, {soundFile:file, autostart:'no', loop:'no'});-->
  <!--    } else if ($('gift_audio_' + id).hasClass('icon_gift_audio_play')) {-->
  <!--      $('gift_audio_' + id).removeClass('icon_gift_audio_play');-->
  <!--      $('gift_audio_' + id).addClass('icon_gift_audio_pause');-->
  <!--      AudioPlayer.embed("song_" + id, {soundFile:file, autostart:'yes', loop:'yes'});-->
  <!---->
  <!--      $('song_wrapper_' + id).setStyle('visibility', 'visible');-->
  <!--      setTimeout(function () {-->
  <!--        $('song_wrapper_' + id).setStyle('visibility', 'hidden');-->
  <!--      }, 100);-->
  <!--    }-->
  <!--  }-->
  <!---->
  <!--  var play_video = function (id, recipeint_id) {-->
  <!--    var url = '--><?php //echo $this->url(array('action' => 'video'), 'hegift_general', true);?><!--' + '/gift_id/' + id + '/recipient_id/' + recipeint_id;-->
  <!--    var $element = new Element('a', {'href':url, 'class':'smoothbox'});-->
  <!--    Smoothbox.open($element, {height:350, width:450});-->
  <!--  }-->

  en4.core.runonce.add(function () {
//    gift_manager.initProfileTips();
  });
</script>
<?php endif; ?>
<?php if ($this->rate) : ?>
<script type="text/javascript">
  en4.core.runonce.add(function () {
    var $stars = $$('.review_widget .rate_star');
    $stars.addEvent('click', function () {
      tl_manager.fireTab('<?php echo $this->rate; ?>');
    });
  });
</script>
<?php endif; ?>
<?php
/**
 * @var $navigation Zend_Navigation
 * @var $nav        Zend_Navigation_Page_Mvc
 */
?>

<div id="tl-navigation" class="tl-block">
  <div class="photo">

    <?php echo $this->htmlLink(
    'javascript:tl_manager.fireTab("timeline")',
    $this->itemPhoto($this->subject(), 'thumb.icon')); ?>
  </div>
  <div class="items tl-options">
    <ul class="main tl-in-block">
      <li>
        <?php echo $this->htmlLink(
        'javascript:tl_manager.fireTab("timeline")',
        $this->subject()->getTitle()); ?>
      </li>
      <?php foreach ($this->widgets as $widget): ?>
      <li>
        <a href="javascript://"
           onclick="tl_manager.fireTab('<?php echo $widget->content_id; ?>')"
           class="tab_button_<?php echo $widget->content_id?>">
          <?php echo $this->translate($this->widget_params[$widget->name]['title']); ?>
          <?php if (array_key_exists('count', $this->widget_params[$widget->name])): ?>
          <?php echo '(' . $this->widget_params[$widget->name]['count'] . ')'; ?>
          <?php endif; ?>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<div class="profile" id='profile'>
<?php echo $this->content()->renderWidget('timeline.page-cover'); ?>

<div class="tl-block info">

<div class="main-row">
  <div class="options">
    <div id='profile-options' class="tl-options">
      <?php if ($this->profile_navigation && $this->profile_navigation->count() > 0): $navigation = $this->profile_navigation->toArray(); ?>
      <ul class="main tl-in-block">
        <?php for ($i = 0; $i < 2; $i++): if (!array_key_exists($i, $navigation)) continue;
        $nav = $navigation[$i];
        $nav['params'] = ((array_key_exists('params', $nav)) && is_array($nav['params'])) ? $nav['params'] : array();
        ?>
        <li><?php
          $tmp_params = array();
          if (isset($nav['module']) && !empty($nav['module']))
            $tmp_params['module'] = $nav['module'];
          if (isset($nav['controller']) && !empty($nav['controller']))
            $tmp_params['controller'] = $nav['controller'];
          if (isset($nav['action']) && !empty($nav['action']))
            $tmp_params['action'] = $nav['action'];
          if (isset($nav['route'])) {
            $link = $this->url(array_merge($tmp_params, $nav['params']), $nav['route']);
          } elseif (isset($nav['url'])) {
            $link = $nav['url'];
          } else {
            $link = $nav['uri'];
          }

          if (isset($nav['href']) && trim($nav['href']) != '')
            $link = $nav['href'];
          echo $this->htmlLink($link, $this->translate($nav['label']),
            array('class' => $nav['class'], 'onClick' => isset($nav['onClick']) ? $nav['onClick'] : '')
          ); ?></li>
        <?php endfor; ?>
        <li class="more <?php if (count($navigation) <= 2): ?> hidden <?php endif; ?>">
          <a id='mp-options' href='javascript:void(0);'
             onclick=""><?php echo $this->translate('more'); ?></a>
          <ul class="tl-in-block click-listener bound-mp-options">
            <?php for ($i = 2; $i < count($navigation); $i++):
            $nav = $navigation[$i];
            $nav['params'] = ((array_key_exists('params', $nav)) && is_array($nav['params'])) ? $nav['params'] : array();
            ?>
            <li> <?php
              if (isset($nav['route'])) {
                $link = $this->url(array_merge($tmp_params, $nav['params']), $nav['route']);
              } elseif (isset($nav['url'])) {
                $link = $nav['url'];
              } else {
                $link = $nav['uri'];
              }
              echo $this->htmlLink($link, $this->translate($nav['label']), array('class' => $nav['class'])); ?>
            </li>
            <?php endfor;?>
          </ul>
        </li>
      </ul>
      <?php endif; ?>
    </div>
  </div>

  <div class="name">
    <div id="profile_photo" class="tl-profile-photo">

      <?php if (isset($this->gift)) : ?>
      <div class="received_gift_info gift_photo_position">
        <img src="<?php echo $this->gift->getPhotoUrl('thumb.normal') ?>" alt="">
        <?php if ($this->privacy) : ?>
        <div class="item_info hidden">
          <div class="manage_gift_tips_details">
            <?php echo $this->htmlLink($this->from->getHref(), $this->itemPhoto($this->from, 'thumb.icon'), array('style' => 'float: left')) ?>
            <div class="manage_gift_details">
              <?php echo $this->htmlLink($this->from->getHref(), $this->from->getTitle()) ?>
              <div
                class="sent_status"><?php echo $this->translate('HEGIFT_sent you this gift ') . '<b>' . $this->recipient->getPrivacy() . '</b>' ?></div>
              <div
                class="gift_manage_info_date"><?php echo $this->translate('HEGIFT_Sent %s ', $this->timestamp($this->recipient->send_date)) ?></div>
            </div>
          </div>
          <?php if ($this->recipient->getMessage()) : ?>
          <div class="clr"></div>
          <div class="manage_gift_message">
            <?php echo $this->recipient->getMessage(); ?>
          </div>
          <?php endif; ?>
        </div>
        <?php if ($this->gift->type === 2) : ?>
          <span class="gift-type-icon icon_gift_audio_play custom_gift_position"
                id="gift_audio_<?php echo $this->gift->getIdentity()?>"
                onclick="play('<?php echo $this->gift->getIdentity()?>', '<?php echo $this->storage->get($this->gift->file_id)->map(); ?>')"
                title="<?php echo $this->translate('HEGIFT_Play')?>">
                            </span>
          <div id="song_wrapper_<?php echo $this->gift->getIdentity(); ?>" class="song_wrapper_gift">
            <div id="song_<?php echo $this->gift->getIdentity(); ?>"></div>
          </div>
          <?php elseif ($this->gift->type === 3) : ?>
          <span class="gift-type-icon icon_gift_video_play custom_gift_position"
                title="<?php echo $this->translate('HEGIFT_Preview')?>"
                onclick="play_video('<?php echo $this->gift->getIdentity()?>', '<?php echo $this->recipient_id?>')">
                            </span>
          <?php endif; ?>

        <?php endif; ?>
      </div>
      <?php endif; ?>

      <a href="<?php echo $this->subject()->getHref(); ?>" id="profile-photo" class="tl-in-block">
        <?php echo $this->itemPhoto($this->subject(), 'thumb.profile'); ?>
      </a>
    </div>

    <div class="tl-profile-title">
      <h2>
        <?php echo $this->htmlLink($this->subject()->getHref(), $this->subject()->getTitle(),
        array('class' => 'profile-title')); ?>
        <?php if ($this->isLikeEnabled): ?>
        <span style="float:right; margin-left: 5px">
              <?php echo $this->likeButton($this->subject()); ?>
            </span>
        <?php endif; ?>
      </h2>
    </div>
  </div>
</div>
<?php if (!$this->private): ?>
<div class="profile-rate" id="profile-rate">
  <?php if ($this->rate) echo $this->content()->renderWidget('rate.widget-rate'); ?>
</div>
<div class="additional-row" id="additional-row">

  <div class="about">
    <?php echo $this->content()->renderWidget('page.profile-fields'); ?>
    <?php echo $this->htmlLink('javascript://', $this->translate('Page Information'), array('title' => $this->subject()->getTitle())); ?>
  </div>

  <div class="applications">
    <div class="application more">
      <div class="controller">
        <span><?php $more = count($this->widgets) - 4; echo ($more > 0) ? $more : 0; ?></span>
      </div>
      <div class="photo"></div>
    </div>

    <?php $i = 0;
    foreach ($this->widgets as $widget):
      try {
        ?>
        <?php if (!array_key_exists($widget->name, $this->noneActiveApplications)): $i++; ?>
          <a href="javascript:tl_manager.fireTab('<?php echo $widget->content_id ?>')"
             class="application <?php if ($i > 4): ?>h hidden<?php endif;?>">
    <?php
          $thumbTable = Engine_Api::_()->getDbTable('thumbs', 'timeline');
          $thumb = $thumbTable->fetchRow($thumbTable->select()
            ->where('type = ?', $widget->name)
            ->limit(1));?>
            <div class="photo <?php if(!($thumb && $thumb->photo_id)) echo str_replace('.', '-', $widget->name); ?>">
              <?php if ($thumb && $thumb->photo_id):?>
                <img style="width: 100%; height: 100%;" src="<?php echo $thumb->getPhotoUrl('thumb.icon'); ?>" alt="">
              <?php else :?>
              <?php if (array_key_exists($widget->name, $this->activeApplications)): ?>
                <?php
                if (array_key_exists('items', $this->activeApplications[$widget->name]) &&
                  ((is_array($this->activeApplications[$widget->name]['items']) && count($this->activeApplications[$widget->name]['items']) > 0)
                    || ($this->activeApplications[$widget->name]['items'] instanceof Zend_Paginator) && $this->activeApplications[$widget->name]['items']->getTotalItemCount() > 0)
                ): $j = 0; ?>
                  <?php foreach ($this->activeApplications[$widget->name]['items'] as $item): ?>
                    <?php if ($j == 3): $j = 0; ?> <br/><?php endif; ?>
                    <?php echo $this->itemPhoto($item, 'thumb.icon');
                    $j++; ?>
                    <?php endforeach; ?>

                  <?php else: ?>
                  <div
                    class="default-<?php echo $this->activeApplications[$widget->name]['module']; ?>">
                    &nbsp;</div>
                  <?php endif; ?>
              <?php elseif (array_key_exists($widget->name, $this->page_widgets)) : ?>
              <div class="default-<?php echo $this->page_widgets[$widget->name]['name']; ?>">&nbsp;</div>
              <?php else : ?>
              <div class="default">&nbsp;</div>
              <?php endif; ?>
             <?php endif; ?>
            </div>

            <div class="title">
              <?php if (array_key_exists($widget->name, $this->page_widgets)) : ?>
              <?php echo  $this->translate($this->widget_params[$widget->name]['title']); ?>
              <?php else : ?>
              <?php echo  $this->translate($this->widget_params[$widget->name]['title']); ?>
              <?php endif; ?>
              <span>
                    <?php if (array_key_exists('count', $this->widget_params[$widget->name])): ?>
                <?php echo $this->widget_params[$widget->name]['count']; ?>
                <?php endif; ?>
                  </span>
            </div>
          </a>
          <?php endif; ?>
        <?php
      } catch (Exception $e) {
        print_log($e);
      }
    endforeach;
    ?>

    <?php
    $to = count($this->widgets) + count($this->noneActiveApplications);
    if ($to < 4) $to = 4;

    for (; $i < $to; $i++):
      ?>
      <?php if (false): //($this->viewer()->getIdentity()): ?>
      <div class="application  <?php if ($i >= 4): ?>h hidden<?php endif;?>">
        <div class="add-app"></div>
        <?php if ($i == ($to - 1)): ?>
        <ul class="available-applications click-listener" id="available-applications">
          <?php foreach ($this->noneActiveApplications as $widget => $application): ?>
          <li><?php echo $this->htmlLink($application['add-link'], $application['title']); ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <div class="photo"></div>
        <div class="title">&nbsp;</div>
      </div>
      <?php endif; ?>
      <?php endfor;?>
  </div>

</div>
  <?php endif; ?>

</div>
</div>

<?php if ($this->private): ?>
<div class="tip private">
  <span><?php echo $this->translate("This profile is private - only friends of this member may view it."); ?></span>
</div>
<?php endif; ?>