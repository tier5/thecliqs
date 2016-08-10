<h2>
  <?php echo $this->translate('User Credits Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<br />
<div class="admin_search">
  <div class="search">
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<br />

<div class="admin_statistics">
  <div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" onclick="processStatisticsPage(-1);"><?php echo $this->translate("Previous") ?></a>
    <a id="admin_stats_offset_next" onclick="processStatisticsPage(1);" style="display: none;"><?php echo $this->translate("Next") ?></a>
  </div>

  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
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
    var updateFormOptions = function() {
      var periodEl = $$('form').getElement('#period');
      var chunkEl = $$('form').getElement('#chunk');
      switch( periodEl.get('value')[0] ) {
        case 'ww':
          var children = chunkEl.getChildren()[0];
          for( var i = 0, l = children.length; i < l; i++ ) {
            if( ['dd'].indexOf(children[i].get('value')) == -1 ) {
              children[i].setStyle('display', 'none');
              if( children[i].get('selected') ) {
                children[i].set('selected', false);
              }
            } else {
              children[i].setStyle('display', '');
            }
          }
          break;
        case 'MM':
          var children = chunkEl.getChildren()[0];
          for( var i = 0, l = children.length; i < l; i++ ) {
            if( ['dd', 'ww'].indexOf(children[i].get('value')) == -1 ) {
              children[i].setStyle('display', 'none');
              if( children[i].get('selected') ) {
                children[i].set('selected', false);
              }
            } else {
              children[i].setStyle('display', '');
            }
          }
          break;
        case 'y':
          var children = chunkEl.getChildren()[0];
          for( var i = 0, l = children.length; i < l; i++ ) {
            if( ['dd', 'ww', 'MM'].indexOf(children[i].get('value')) == -1 ) {
              children[i].setStyle('display', 'none');
              if( children[i].get('selected') ) {
                children[i].set('selected', false);
              }
            } else {
              children[i].setStyle('display', '');
            }
          }
          break;
        default:
          break;
      }
      
    }
    var buildStatisticsSwiff = function(args) {
      currentArgs = args;

      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));

      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-data')) ?>');
      url.setData(args);
      
      //$('my_chart').empty();
      swfobject.embedSWF(
        "<?php echo $this->baseUrl()?>/externals/open-flash-chart/open-flash-chart.swf",
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
   
    window.addEvent('load', function() {
      updateFormOptions();
      $('period').addEvent('change', function(event) {
        updateFormOptions();
      });
      buildStatisticsSwiff({
        'type' : 'core.views',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0
      });
    });
  </script>
  <div id="my_chart"></div>
</div>
