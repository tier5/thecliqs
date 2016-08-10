<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl  28.11.11 18:04 TeaJay $
 * @author     Taalay
 */
?>

<?php if (!$this->last_id) : ?>
  <?php
    $this->headScript()
      ->appendFile('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places')
      ->appendFile($this->baseUrl() . '/application/modules/Checkin/externals/scripts/core.js');
   ?>

  <?php if ($this->markers && count($this->activity) > 0) : ?>
    <div class="checkin_view_mode" style="overflow: hidden; margin: 10px;">
      <h3 class="checkin_title"><?php echo $this->translate('CHECKIN_VIEW_MODE_DESC')?></h3>
      <ul>
        <li><a class="map checkin-view-types" title="<?php echo $this->translate('CHECKIN_map view'); ?>" onfocus="this.blur();" href="javascript://" onclick="checkin_map.setView('map', $(this));"></a></li>
        <li><a class="list active checkin-view-types" title="<?php echo $this->translate('CHECKIN_list view'); ?>" onfocus="this.blur();" href="javascript://" onclick="checkin_map.setView('list', $(this));"></a></li>
      </ul>
    </div>

    <script type="text/javascript">
      en4.core.runonce.add(function() {
        checkin_map.construct( null, <?php echo $this->markers; ?>, 4, <?php echo $this->bounds; ?> );
      });
    </script>

    <div style="position: relative;">
      <div id="" style="overflow: hidden;">
        <div id="map_canvas" class="browse_gmap" style="position: absolute; top: 100000px; height: 460px;"></div>
      </div>
    </div>
  <?php endif; ?>

<div id="checkin_list_cont">
<?php endif; ?>
<?php if( !empty($this->feedOnly) && empty($this->checkUpdate)): // ajax?>
  <?php echo $this->checkinActivityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'comment_pagination' => $this->comment_pagination,
    'matchedCheckinsCount' => $this->matchedCheckinsCount
  ));?>

  <?php if ($this->nextid && !$this->endOfFeed):?>
    <li class="utility-viewall">
      <div class="pagination">
        <a href="javascript:void(0);" rev="next_<?php echo $this->nextid?>"><?php echo $this->translate('View More')?></a>
      </div>
      <div class="loader" style="display: none;">
        <div class="wall_icon"></div>
        <div class="text">
          <?php echo $this->translate('Loading ...')?>
        </div>
      </div>
    </li>
  <?php endif;?>

  <?php if ($this->firstid):?>
    <li class="utility-setlast wall_displaynone" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
  <?php endif;?>

  <?php return; ?>
<?php endif; ?>

<?php echo $this->render('_header.tpl'); ?>

<script type="text/javascript">
  Wall.runonce.add(function (){
    var feed = new Wall.Feed({
      feed_uid: '<?php echo $this->feed_uid?>',
      enableComposer: 0,
      url_wall: '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'checkin.profile-checkins'), 'default', true) ?>',
      last_id: <?php echo sprintf('%d', $this->firstid) ?>,
      subject_guid : '<?php echo $this->subjectGuid ?>'
    });
  });
</script>

<div class="wallFeed" id="<?php echo $this->feed_uid?>">
  <div class="wall-streams">
    <div class="wall-stream wall-stream-social is_active">
      <ul class="wall-feed checkins feed" id="activity-feed">
        <?php if( $this->activity ): ?>
          <?php echo $this->checkinActivityLoop($this->activity, array(
            'action_id' => $this->action_id,
            'viewAllComments' => $this->viewAllComments,
            'viewAllLikes' => $this->viewAllLikes,
            'comment_pagination' => $this->comment_pagination,
          ))?>
        <?php endif; ?>
        <?php if ($this->nextid && !$this->endOfFeed):?>
          <li class="utility-viewall">
            <div class="pagination">
              <a href="javascript:void(0);" rev="next_<?php echo $this->nextid?>"><?php echo $this->translate('View More')?></a>
            </div>
            <div class="loader" style="display: none;">
              <div class="wall_icon">&nbsp;</div>
              <div class="text">
                <?php echo $this->translate('Loading ...')?>
              </div>
            </div>
          </li>
        <?php endif;?>
        <?php if( !$this->activity ): ?>
          <li class="wall-empty-feed">
            <div class="tip">
              <span>
                <?php echo $this->translate("WALL_EMPTY_FEED") ?>
              </span>
            </div>
          </li>
        <?php endif; ?>
        <?php if ($this->firstid):?>
          <li class="utility-setlast" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
        <?php endif;?>
      </ul>
    </div>
    <?php
    if ($this->viewer()->getIdentity() && !$this->subject()){
      foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class || !$class->isActiveStream()){
          continue ;
        }
        $tpl = $class->getFeedTpl();
        echo '<div class="wall-stream wall-stream-'.$service.'">
          <ul>
            <li class="wall-stream-tab-login wall-stream-tab">
              <div class="tip"><span>
              '.$this->translate('WALL_STREAM_'.strtoupper($service).'_LOGIN', array('<a href="javascript:void(0);" class="stream_login_link">', '</a>')).'
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
<?php if (!$this->last_id) : ?>
</div>
<?php endif; ?>