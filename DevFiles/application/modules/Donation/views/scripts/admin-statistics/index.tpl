<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 24.08.12
 * Time: 14:34
 * To change this template use File | Settings | File Templates.
 */?>

<h2><?php echo $this->translate("Donation Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
<div class='donation_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class="clr"></div>

<div class="menu_right" style="width:200px">
  <ul class="menu_dashboard_links">
    <li style="width:200px">
      <ul >
        <li class="hecore-menu-tab active-menu-tab">
          <a href="<?php echo $this->url(array('module' => 'donation' ,'controller'=>'statistics', 'action' => 'index'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('DONATION_Chart'); ?>
          </a>
        </li>

        <li class="hecore-menu-tab">
          <a href="<?php echo $this->url(array('module' => 'donation', 'controller'=>'statistics', 'action' => 'list'),'admin_default', true); ?>" class="hecore-menu-link">
            <?php echo $this->translate('DONATION_List'); ?>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</div>

<div class="admin_home_middle" style="clear: none;">

  <h3><?php echo $this->translate("DONATION_Statistics") ?></h3>
  <p>
    <?php echo $this->translate("DONATION_VIEWS_SCRIPTS_ADMINSTATS_INDEX_DESCRIPTION") ?>
  </p>

  <br />

  <div class="admin_search">
    <div class="search donation">
      <?php echo $this->filterForm->render($this) ?>
    </div>
  </div>

</div>

<div class="admin_statistics">
  <div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);">
      <?php echo $this->translate("Previous") ?>
    </a>
    <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;">
      <?php echo $this->translate("Next") ?>
    </a>
    <div class="clr"></div>
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

      var url = new URI('<?php echo $this->url(array('action' => 'chart-data')) ?>');
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
        'type' : '0',
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
