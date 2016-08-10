<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<?php if (!empty($this->feedOnly) && empty($this->checkUpdate)): // ajax?>

<?php echo $this->wallActivityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'comment_pagination' => $this->comment_pagination,
    'timelineData' => $this->timelineData,
    'module' => 'timeline'
  ));?>


<?php if ($this->lastid && !$this->endOfFeed): ?>

  <li class="utility-viewall sep active <?php echo $this->lastdate; ?>">
    <div class="pagination">
      <a href="javascript:void(0);"
         rev="nextid<?php echo $this->lastid?>|nextdate<?php echo $this->lastdate?>"><?php echo $this->translate('View More')?></a>
    </div>
    <div class="loader" style="display: none;">
        <span>
        <div class="wall_icon"></div>
        <div class="text"><?php echo $this->translate('Loading ...')?></div>
        </span>
    </div>
  </li>

  <?php elseif ($this->showBorn): ?>

  <script type="text/javascript">
    timeline.scroller.lifeEvent('born', true);
  </script>

  <?php endif; ?>

<?php if ($this->firstid): ?>
  <li class="utility-setlast wall_displaynone"
      rev="item_<?php echo sprintf('%d|%s', $this->firstid, $this->firstdate); ?>"></li>
  <?php endif; ?>

<?php return; ?>

<?php endif; ?>

<?php if (!empty($this->checkUpdate)): ?>
<?php if ($this->activityCount): ?>

  <li class="utility-getlast">

    <script type='text/javascript'>
      Wall.activityCount(<?php echo $this->activityCount?>);
    </script>

    <div class='tip'>
        <span>
          <a href='javascript:void(0);' class="link">
            <?php echo $this->translate(array(
              '%d new update is available - click this to show it.',
              '%d new updates are available - click this to show them.',
              $this->activityCount),
            $this->activityCount)?>
          </a>
        </span>
    </div>

  </li>

  <?php return; ?>


  <?php endif; ?>
<?php return; ?>

<?php endif; ?>

<?php
    echo $this->render('_wallHeader.tpl');
?>
<script type="text/javascript">
  Wall.runonce.add(function () {
    var feed = new Wall.Feed({
      feed_uid:'<?php echo $this->feed_uid?>',
      enableComposer: <?php echo ($this->enableComposer) ? 1 : 0?>,
      url_wall:'<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'timeline.page-feed'), 'default', true) ?>',
      last_id: <?php echo sprintf('%d', $this->firstid) ?>,
      last_date:'<?php echo $this->firstdate; ?>',
      subject_guid:'<?php echo $this->subjectGuid ?>',
      fbpage_id: <?php echo ($this->fbpage_id) ? "'{$this->fbpage_id}'" : 0;?>
    });
    feed.compose.is_timeline = 1;//(Boolean)(<?php //echo (int)$this->composerOnly; ?>);
    feed.params = <?php echo $this->jsonInline($this->list_params);?>;
  <?php if (!$this->composerOnly && empty($this->action_id)): ?>

    <?php if ($this->updateSettings): ?>

      feed.watcher = new Wall.UpdateHandler({
        baseUrl:en4.core.baseUrl,
        basePath:en4.core.basePath,
        identity:4,
        delay: <?php echo $this->updateSettings;?>,
        last_id: <?php echo sprintf('%d', $this->firstid) ?>,
        last_date:'<?php echo $this->firstdate ?>',
        subject_guid:'<?php echo $this->subjectGuid ?>',
        feed_uid:'<?php echo $this->feed_uid?>'
      });

      try {
        setTimeout(function () {
          feed.watcher.start();
        }, 1250);
      } catch (e) {
      }

      <?php endif; ?>

    <?php else: ?>

    var tab_link = $$('.tab_layout_wall_feed')[0];
    if (tabContainerSwitch) {
      tabContainerSwitch(tab_link, 'generic_layout_container layout_wall_feed');
    }

    <?php endif;?>

  });
</script>





<div class="wallFeed" id="<?php echo $this->feed_uid?>">


<?php if ($this->viewer()->getIdentity() && !$this->subject()): ?>

<div class="wall-stream-header">
  <ul class="wall-stream-types">
    <li>

      <a href="javascript:void(0);" class="is_active wall-stream-type wall-stream-type-social wall_blurlink"
         rev="social">
        <span class="wall_icon">&nbsp;</span>
        <?php echo $this->translate('WALL_STREAM_SOCIAL');?>
      </a>

      <?php

      foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service) {
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class || !$class->isActiveStream()) {
          continue;
        }
        echo '<li>';
        echo '<a href="javascript:void(0);" class="wall-stream-type wall-stream-type-' . $service . ' wall_blurlink" rev="' . $service . '"><span class="wall_icon">&nbsp;</span>' . $this->translate('WALL_STREAM_' . strtoupper($service)) . '</a>';
        echo '</li>';
      }
      ?>

    </li>
  </ul>


  <ul class="wall-stream-options">

    <li class="wall-stream-option wall-stream-option-social is_active">

      <div class="wall-lists">
        <?php echo $this->partial('_activeList.tpl', 'wall', array(
        'list_params' => $this->list_params,
        'types' => $this->types,
        'lists' => $this->lists
      ))?>
        <ul class="wall-types">
          <?php echo $this->partial('_list.tpl', 'wall', array(
          'list_params' => $this->list_params,
          'types' => $this->types,
          'lists' => $this->lists
        ))?>
        </ul>
      </div>

    </li>

    <?php
    // or js inject
    ?>

    <li class="wall-stream-option wall-stream-option-facebook">

      <ul class="wall-options">
        <li>
          <a href="javascript:void(0);" class="wall-button-icon wall_blurlink wall_tips"
             title="<?php echo $this->translate("WALL_REFRESH")?>">
            <span class="wall_icon wall-refresh"></span>
          </a>
        </li>
        <li>
          <a href="javascript:Wall.services.get('facebook').logout();" class="wall-button-icon wall_blurlink wall_tips"
             title="<?php echo $this->translate("WALL_SERVICE_LOGOUT")?>">
            <span class="wall_icon wall-logout"></span>
            &nbsp;
          </a>
        </li>
      </ul>

    </li>

    <li class="wall-stream-option wall-stream-option-twitter">

      <ul class="wall-options">
        <li>
          <a href="javascript:void(0);" class="wall-button-icon wall_blurlink wall_tips"
             title="<?php echo $this->translate("WALL_REFRESH")?>">
            <span class="wall_icon wall-refresh"></span>
          </a>
        </li>
        <li>
          <a href="javascript:Wall.services.get('twitter').logout();" class="wall-button-icon wall_blurlink wall_tips"
             title="<?php echo $this->translate("WALL_SERVICE_LOGOUT")?>">
            <span class="wall_icon wall-logout"></span>
            &nbsp;
          </a>
        </li>
      </ul>

    </li>


  </ul>
</div>

  <?php endif;?>

<div class="wall-streams">

  <div class="wall-stream wall-stream-social is_active">

    <?php if ($this->enableComposer): ?>

    <div class="wallComposer wall-social-composer tli l">

      <form method="post" action="<?php echo $this->url()?>">

        <div class="wallComposerContainer">
          <div class="wallTextareaContainer">
            <div class="inputBox">
              <div class="labelBox is_active">
                <span><?php echo $this->translate('WALL_Post Something...');?></span>
              </div>
              <div class="textareaBox">
                <div class="close"></div>
                <textarea rows="1" cols="1" name="body"></textarea>
                <input type="hidden" name="return_url" value="<?php echo $this->url() ?>"/>
                <?php if ($this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
                <input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>"/>
                <?php endif; ?>
              </div>
            </div>
            <div class="toolsBox"></div>

          </div>
        </div>

        <div class="wall-compose-tray"></div>

        <div class="submitMenu">
          <button type="submit">&nbsp;&nbsp;&nbsp;<?php echo $this->translate("WALL_Share") ?>
            &nbsp;&nbsp;&nbsp;</button>

          <?php if ($this->allowPrivacy): ?>

          <div class="wall-privacy-container">
            <a href="javascript:void(0);" class="wall-privacy-link wall_tips wall_blurlink"
               title="<?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_' . strtoupper($this->privacy_active));?>">
              <span class="wall_privacy">&nbsp;</span>
              <span class="wall_expand">&nbsp;</span>
            </a>
            <ul class="wall-privacy">
              <?php foreach ($this->privacy as $item): ?>
              <li>
                <a href="javascript:void(0);"
                   class="item wall_blurlink <?php if ($item == $this->privacy_active): ?>is_active<?php endif;?>"
                   rev="<?php echo $item?>">
                  <span class="wall_icon_active">&nbsp;</span>
                  <span
                    class="wall_text"><?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_' . strtoupper($item));?></span>
                </a>
              </li>
              <?php endforeach;?>
            </ul>
            <input type="hidden" name="privacy" value="<?php echo $this->privacy_active;?>" class="wall_privacy_input"/>
          </div>

          <?php endif;?>

          <ul class="wallShareMenu">
            <?php

            if ($this->viewer()->getIdentity()) {

              $setting = Engine_Api::_()->wall()->getUserSetting($this->viewer());

              foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service) {
                $class = Engine_Api::_()->wall()->getServiceClass($service);
                if (!$class || !$class->isActiveShare()) {
                  continue;
                }

                $a_class = 'wall-share-' . $service . ' wall_tips disabled';

                echo '<li class="service">
                        <a href="javascript:void(0);" class="' . $a_class . '" rev="' . $service . '" title="' . $this->translate('WALL_SHARE_' . strtoupper($service) . '') . '"></a>
                        <input type="hidden" name="share[' . $service . ']" class="share_input" value="0"/>
                      </li>';

              }
            }
            ?>
          </ul>
        </div>


        <?php foreach ($this->composePartials as $partial): ?>
        <?php echo $this->partial($partial[0], $partial[1], array(
          'feed_uid' => $this->feed_uid,
          'subject_uid' => $this->subject()->getIdentity()
        )) ?>
        <?php endforeach; ?>

      </form>
      <div class="arrow"></div>
      <div class="dot">
        <div></div>
      </div>
    </div>

    <?php endif;?>

    <ul class="wall-feed feed" id="activity-feed">
      <?php if (!$this->composerOnly): ?>
      <?php if ($this->activity): ?>
        <?php echo $this->wallActivityLoop($this->activity, array(
          'action_id' => $this->action_id,
          'viewAllComments' => $this->viewAllComments,
          'viewAllLikes' => $this->viewAllLikes,
          'comment_pagination' => $this->comment_pagination,
          'timelineData' => $this->timelineData,
          'module' => 'timeline',
        ))?>
        <?php endif; ?>

      <?php if ($this->lastid && !$this->endOfFeed): ?>
        <li class="utility-viewall sep active <?php echo $this->lastdate; ?>">
          <div class="pagination">
            <a href="javascript:void(0);"
               rev="nextid<?php echo $this->lastid?>|nextdate<?php echo $this->lastdate?>"><?php echo $this->translate('View More')?></a>
          </div>
          <div class="loader" style="display: none;">
                <span>
                <div class="wall_icon">&nbsp;</div>
                <div class="text"><?php echo $this->translate('Loading ...')?></div>
                </span>
          </div>
        </li>

        <?php elseif ($this->showBorn) : ?>

        <script type="text/javascript">
          Wall.runonce.add(function () {
            timeline.scroller.lifeEvent('born', true);
          });
        </script>

        <?php endif; ?>

      <?php if (!$this->activity): ?>
        <li class="wall-empty-feed">
          <div class="tip">
                  <span>
                    <?php echo $this->translate("WALL_EMPTY_FEED") ?>
                  </span>
          </div>
        </li>
        <?php endif; ?>

      <?php if ($this->firstdate): ?>
        <li class="utility-setlast" rev="item_<?php echo sprintf('%d|%s', $this->firstid, $this->firstdate); ?>"></li>
        <?php endif; ?>
      <?php endif; ?>
    </ul>
  </div>


  <?php

  if ($this->viewer()->getIdentity() && !$this->subject()) {

    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service) {
      $class = Engine_Api::_()->wall()->getServiceClass($service);
      if (!$class || !$class->isActiveStream()) {
        continue;
      }

      $tpl = $class->getFeedTpl();

      echo '<div class="wall-stream wall-stream-' . $service . '">
          <ul>
            <li class="wall-stream-tab-login wall-stream-tab">
              <div class="tip"><span>
              ' . $this->translate('WALL_STREAM_' . strtoupper($service) . '_LOGIN', array('<a href="javascript:void(0);" class="stream_login_link">', '</a>')) . '
              </span></div>
            </li>
            <li class="wall-stream-tab-loader wall-stream-tab"><div class="wall-loader"></div></li>
            <li class="wall-stream-tab-stream wall-stream-tab">';

      echo $this->partial(@$tpl['path'], @$tpl['module'], array('feed_uid' => $this->feed_uid));

      echo "</li></ul></div>";

    }

  }
  ?>

</div>

</div>
