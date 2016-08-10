<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: date.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<?php $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Timeline/externals/scripts/composer_date.js') ?>

<script type="text/javascript">

  Wall.runonce.add(function () {

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    feed.compose.addPlugin(new Wall.Composer.Plugin.Date({
      title:'<?php echo $this->string()->escapeJavascript($this->translate('Select a date')) ?>',
      lang:{
        'cancel':'<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>'
      },
      requestOptions:{
        'url':'<?php echo $this->url(array('id' => $this->subject_uid), 'timeline_date', true); ?>'
      }
    }));

  });

</script>