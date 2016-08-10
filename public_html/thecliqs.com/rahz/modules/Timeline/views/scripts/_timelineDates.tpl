<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _timelineDates.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<?php
if (!$this->htmlOnly): ?>
<script type="text/javascript">
    en4.core.runonce.add(function () {
        if (typeof(timeline) == 'object') {
            timeline.now = <?php echo Zend_Json::encode(@$this->dates['now']); ?>;

            <?php if (array_key_exists('last_month', $this->dates)): ?>
                timeline.last_month = <?php echo Zend_Json::encode($this->dates['last_month']); ?>;
                <?php endif; ?>

            <?php if (array_key_exists('years', $this->dates)): ?>
                timeline.years = <?php echo Zend_Json::encode($this->dates['years']); ?>;
                <?php endif; ?>

            timeline.options.dates_url = <?php echo Zend_Json::encode($this->url(
                array('id' => $this->subject()->getIdentity()), 'timeline_dates', true)); ?>;
            timeline.options.life_event_url = <?php echo Zend_Json::encode($this->url(
                array('id' => $this->subject()->getIdentity(), 'subject' => $this->subject()->getType()), 'timeline_life_event', true)); ?>;

            timeline.isOwner = <?php echo (int)$this->subject()->isSelf($this->viewer()); ?>;
            timeline.init();
        }
    });
</script>
<?php endif; ?>


<li class="active" rev='now'>
    <span class="month">
      <a href="javascript://"><?php echo $this->translate('Now'); ?></a>
    </span>
</li>

<?php if (count($this->dates) > 0): ?>
<?php if (isset($this->dates['last_month'])): ?>
    <li rev="<?php echo $this->dates['last_month']['year'] . '-' . $this->dates['last_month']['month']; ?>">
    <span class="month">
      <a href="javascript://"><?php echo ($this->dates['last_month']['title']); ?></a>
    </span>
    </li>
    <?php endif; ?>

<?php if (array_key_exists('years', $this->dates)): ?>
    <?php foreach ($this->dates['years'] as $title => $year): ?>
        <li rev="<?php echo substr($title, 1); ?>">
      <span class="year" rev="<?php echo substr($title, 1); ?>">
        <a href="javascript://"><?php echo substr($title, 1); ?></a>
      </span>

            <ul class="months">
                <?php foreach ($year as $month): ?>
                <li rev="<?php echo $month['year'] . '-' . $month['month']; ?>"
                    title="<?php echo $month['name'] . ', ' . $month['year']; ?>">
          <span class="month" rev="<?php echo $month['max_id'] . ' ' . $month['min_id'] ?>">
            <a href="javascript://"><?php echo ($month['title']); ?></a>
          </span>
                </li>
                <?php endforeach; ?>
            </ul>

        </li>
        <?php endforeach; ?>
    <?php endif; ?>

<?php if (array_key_exists('born', $this->dates)): ?>
    <?php $lang = 'Born';
        if ($this->subject()->getType() == 'page') $lang = 'Created'; ?>
    <li rev="born">
      <span class="life-event"
            rev="<?php echo $this->dates['born']['year'] . '-' . $this->dates['born']['month'] . '-' . $this->dates['born']['day']?>"
            title="<?php echo $this->translate($lang); ?>">
        <a href="javascript://"><?php echo $this->translate($lang); ?></a>
      </span>
    </li>
    <?php endif; ?>

<?php endif; ?>