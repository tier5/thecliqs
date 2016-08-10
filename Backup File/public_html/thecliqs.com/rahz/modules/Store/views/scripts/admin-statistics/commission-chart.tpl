<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: commission-chart.tpl  16.05.12 18:35 TeaJay $
 * @author     Taalay
 */
?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php echo $this->getGatewayState(0); ?>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php echo $this->render('admin/_statisticsMenu.tpl'); ?>

<br/>

<div class="settings admin_home_middle" style="clear: none;">

  <h3><?php echo $this->translate("STORE_Product Statistics") ?></h3>
  <p>
    <?php echo $this->translate("STORE_Commission Chart") ?>
  </p>

  <br />

  <div class="admin_search">
    <div class="search store">
      <?php echo $this->filterForm->render($this) ?>
    </div>
  </div>

</div>

<br/>

<div class="admin_statistics">
  <div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>&nbsp;&nbsp;
    <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
  </div>

  <script type="text/javascript" src="externals/swfobject/swfobject.js"></script>
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

      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

      var url = new URI('<?php echo $this->url(array('action' => 'chart-data', 'chart' => 2, 'type' => 'commission')) ?>');
      url.setData(args);

      //$('my_chart').empty();
      swfobject.embedSWF(
        "<?php echo $this->layout()->staticBaseUrl ?>externals/open-flash-chart/open-flash-chart.swf",
        "my_chart",
        "850",
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
        'type' : 'commission',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0,
        'chart' : 2
      });
    });
  </script>
  <div id="my_chart"></div>
</div>