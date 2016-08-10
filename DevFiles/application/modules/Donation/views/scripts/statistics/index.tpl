<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 23.08.12
 * Time: 17:47
 * To change this template use File | Settings | File Templates.
 */?>
<?php if ($this->subject):?>
  <?php echo $this->render('editMenu.tpl')?>
<?php endif; ?>
<div class="headline">
  <h2><?php echo $this->translate('DONATION_Chart Statistic');?></h2>
</div>
<div class="clr"></div>

<?php echo $this->render('statistics.tpl');?>

<div class="layout_middle" style="clear: none;">

  <p>
    <?php echo $this->translate("DONATION_VIEWS_SCRIPTS_STATISTICS_CHART_DESCRIPTION") ?>
  </p>

  <br />

  <div><?php echo $this->filterForm->render($this); ?></div>

</div>

<div class="page_stat">
  <div class="page_statistics_nav">
    <a id="page_stats_offset_previous" onclick="processStatisticsPage(-1);">
      <?php echo $this->translate("Previous") ?>
    </a>
    <a id="page_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;">
      <?php echo $this->translate("Next") ?>
    </a>
    <div class="clr"></div>
  </div>

  <?php echo $this->headScript()->appendFile($this->layout()->staticBaseUrl . 'externals/swfobject/swfobject.js') ?>
  <script type="text/javascript">
    var currentArgs = {};

    var processStatisticsFilter = function(formElement) {
      var vals = formElement.toQueryString().parseQueryString();
      vals.offset = 0;
      buildStatisticsSwiff(vals);
      return false;
    }

    var processStatisticsPage = function(count) {
      var args = $merge(currentArgs);
      args.offset += count;
      buildStatisticsSwiff(args);
    }

    var buildStatisticsSwiff = function(args) {
      currentArgs = args;

      $('page_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

      var url = new URI('<?php echo $this->url(array('action' => 'chart-data')); ?>');
      url.setData(args);
      $('my_chart').empty();
      swfobject.embedSWF(
        "<?php echo $this->layout()->staticBaseUrl ?>externals/open-flash-chart/open-flash-chart.swf",
        "my_chart",
        "900",
        "400",
        "9.0.0",
        "expressInstall.swf",
        {
          "data-file" : escape(url.toString()),
          'id' : 'mooo'
        }
      );
    }

    /* OFC */
    var ofcIsReady = false;
    function ofc_ready()
    {
      ofcIsReady = true;
    }
    var save_image = function() {
      var img_src = "<img src='data:image/png;base64," + $('my_chart').get_img_binary() + "' />";
      var img_win = window.open('', 'Charts: Export as Image');
      img_win.document.write("<html><head><title>Charts: Export as Image</title></head><body>" + img_src + "</body></html>");

      return;

      var url = '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-image-upload')) ?>';
      $('my_chart').post_image(url, 'onImageUploadComplete', false);
    }

    var onImageUploadComplete = function() {

    }

    window.addEvent('load', function() {
      buildStatisticsSwiff({
        'type' : 'gross',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'MM',
        'start' : 0,
        'offset' : 0
      });
    });
  </script>


  <div id="my_chart"></div>
</div>
