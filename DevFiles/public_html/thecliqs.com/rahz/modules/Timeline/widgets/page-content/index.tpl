<?php
/**
 * SocialEngine
 *
 * @category Application_Extensions
 * @package Timeline
 * @copyright Copyright Hire-Experts LLC
 * @license http://www.hire-experts.com
 * @version Id: index.tpl 2/11/12 3:25 PM mt.uulu $
 * @author Mirlan
 */
?>

<?php
$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/page_timeline/page_timeline.js')
  ->appendFile($this->baseUrl() . '/application/modules/Timeline/externals/scripts/page_timeline/page_born.js')
  ;
?>

<script type="text/javascript">
  var timeline = new PageTimeLine();
</script>

<div class="tl-right">
  <ul id='tl-dates'>
    <?php echo $this->partial('_timelineDates.tpl', null, array(
    'dates' => $this->dates,
    'subject_uid' => $this->subject_uid,
  )); ?>
  </ul>
</div>

<div class="tl-content <?php if( !$this->subject()->isSelf($this->viewer())): ?>none-active<?php endif; ?>" >

  <div id="timeline">
    <div class="line"></div>
    <div class="plus">
      <div>
        <div class="ver"></div>
        <div class="hor"></div>
      </div>
    </div>
  </div>
  <div id="tl-page-feed">
    <div class="loader">
      <a class="buttonlink icon_loading"><?php echo $this->translate('Loading'); ?></a>
    </div>

    <?php echo $this->content()->renderWidget('timeline.page-feed'); ?>
  </div>

  <?php if($this->subject()->isSelf($this->viewer())): ?>
  <div id="tl-composer" class="click-listener bound-timeline">
    <?php echo $this->content()->renderWidget('timeline.page-feed', array('composerOnly' => true)); ?>
  </div>
  <?php endif; ?>
</div>