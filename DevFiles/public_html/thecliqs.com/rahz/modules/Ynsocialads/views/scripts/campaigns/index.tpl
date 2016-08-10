<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynsocialads/externals/scripts/moo.flot.js');
?>

<p>
	<?php echo $this->translate("YNSOCIALADS_MY_CAMPAIGN_DESCRIPTION") ?>
</p>

<div id="my_campaigns">
<div class="yn_search frontend_search">
    <?php echo $this->form->render($this);?>
</div>
<?php $campaign_ids = "";
if( count($this->paginator) ): ?>
<div class="fixed-scrolling">
<table class='ynsocial_table frontend_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate('Name') ?></th>
      <th><?php echo $this->translate('Ads') ?></th>
      <th><?php echo $this->translate('Status') ?></th>
      <th><?php echo $this->translate('Impressions') ?></th>
      <th><?php echo $this->translate('Clicks') ?></th>
      <th><?php echo $this->translate('Options') ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): 
    	$campaign_ids .= $item -> getIdentity(). ","; ?>
      <?php $countDetail = $item->countDetail();?>
      <tr>
        <td><?php echo $this->htmlLink(array(
            'route' => 'ynsocialads_ads',
            'module' => 'ynsocialads',
            'controller' => 'ads',
            'campaign_id' => $item->campaign_id
        ), $this->translate($item->title), array()) ?></td>
        <td><?php echo $countDetail['ads'] ?></td>
        <td><?php echo ucfirst($this->translate($item->status)) ?></td>
        <td><?php echo $countDetail['impressions'] ?></td>
        <td><?php echo $countDetail['clicks'] ?></td>
        <td>
            <?php if ($item->status == 'active') { ?>
            <?php echo $this->htmlLink(
            array('route' => 'ynsocialads_campaigns', 'module' => 'ynsocialads', 'controller' => 'campaigns', 'action' => 'edit', 'id' => $item->campaign_id), 
            $this->translate('edit'), 
            array('class' => 'smoothbox')
            )?>
             | 
            <?php echo $this->htmlLink(
            array('route' => 'ynsocialads_campaigns', 'module' => 'ynsocialads', 'controller' => 'campaigns', 'action' => 'delete', 'id' => $item->campaign_id),
            $this->translate("delete"),
            array('class' => 'smoothbox')
            )?>
             |  
            <?php } ?>
            <?php echo $this->htmlLink(
            array(
                'route' => 'ynsocialads_ads',
                'module' => 'ynsocialads',
                'controller' => 'ads',
                'campaign_id' => $item->campaign_id
            ), 
            $this->translate('View Ads'), 
            array()
            )?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo ($this->translate('Total').' '.$total.' '.$this->translate('result(s)'));
    echo '</p>';
}?>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('Don\'t have any campaigns.') ?>
    </span>
  </div>
<?php endif; ?>
<div class="yn_filter">
    <?php echo $this->formStatistic->render($this) ?>
</div>
<br />
<div class="admin_statistics_nav">
    <a id="admin_stats_offset_previous" class="add_link" href="" onclick="processStatisticsPage(-1, event);"><?php echo $this->translate("Previous") ?></a>
    <a id="admin_stats_offset_next" class="add_link" href="" onclick="processStatisticsPage(1, event);" style="display: none;"><?php echo $this->translate("Next") ?></a>
</div>
<br />
<div class="admin_statistics">
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
  <script type="text/javascript">
    
    var updateFormOptions = function() {
      var periodEl = $('statistic_form').getElement('#period');
      var chunkEl = $('statistic_form').getElement('#chunk');
      switch( periodEl.get('value')) {
        case 'ww':
          var children = chunkEl.getChildren();
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
          var children = chunkEl.getChildren();
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
          var children = chunkEl.getChildren();
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
    
    var currentArgs = {};
    var processStatisticsFilter = function(formElement) {
      var vals = formElement.toQueryString().parseQueryString();
      vals.offset = 0;
      buildStatisticsSwiff(vals);
      return false;
    }
    
    var processStatisticsPage = function(count, event) {
      event.preventDefault();
      var args = $merge(currentArgs);
      args.offset += count;
      buildStatisticsSwiff(args);
    }
    var buildStatisticsSwiff = function(args) {
      currentArgs = args;
      $('admin_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));
      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart')) ?>');
      url.setData(args);
      new Request.JSON({
            method: 'post',
            url: url,
            data: {
            	'campaign_ids': '<?php echo $campaign_ids ?>'
            },
           	onSuccess: function(responseJSON) 
            {
                var tooltip = new Element('div', {
                    id: "tooltip"
                });
                var json_data = responseJSON.json;
                var d = [];
                var d2 = [];
                var ticks = [];
                var count = 0;
                
                for(var i in json_data)
                {
                    d.push([count, json_data [i]]);
                    ticks.push([count, i]);
                    count = count +1;
                }   
                
				count = 0;
                if(responseJSON.json2)
                {
                	var json_data2 = responseJSON.json2;
                	for(var i in json_data2)
	                {
	                    d2.push([count, json_data2 [i]]);
	                    count = count +1;
	                }   
                }
				
                var data = [];
                switch(args.type) {
                    case "click":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Clicks")?>'
		                }];
                        break;
                    case "impression":
                        var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Impressions")?>'
		                }];
                        break;
                    case "all":
                    	var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Clicks")?>'
		                },{
					        data: d2,
					        label: '<?php echo $this -> translate("Impressions")?>'
					    }];
                        break;
                }
                var title_data = responseJSON.title;
                 flot.plot(document.id('placeholder'), data, {
                    legend: {
                        labelFormatter: function(label, series) {
                            return  label + " - " + title_data;
                        }
                    },
                    series: {
                        lines: {
                            show: true
                        },
                        points: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true
                    },
                    xaxis: { 
                        show: true,
                        ticks: ticks
                    }
                });
                tooltip.inject(document.body);
                
                document.id('placeholder').addEvent('plothover', function (event, pos, items) {
                    if (items) {
                        var html = '';
                        items.each(function (el) {
                            var y = el.datapoint[1].toFixed(2);
                            html += el.series.label + " of " + el.series.xaxis.ticks[el.dataIndex].label + " = " + y + "<br />";
                        });
            
                        $("tooltip").set('html', html).setStyles({
                            top: items[0].pageY,
                            left: items[0].pageX
                        });
                        $("tooltip").fade('in');
                    } else {
                        $("tooltip").fade('out');
                    }
                });
                
                if(args.chunk == "dd" && args.period =="y")
                {
                    $$('.xAxis .tickLabel').setStyle('display', 'none');
                }
                	document.id('placeholder').addEvent('plotclick', function (event, pos, items) {
                });
            }
        }).send();
    }

    window.addEvent('load', function() {
      updateFormOptions();
      $('period').addEvent('change', function(event) {
        updateFormOptions();
      });
      buildStatisticsSwiff({
        'type' : 'all',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0
      });
      
      
    });
  </script>
  <div class="fixed-scrolling">
    <div id="placeholder" style="width:800px; height:350px;">
      <div id="clickInfo"></div> 
    </div>
  </div>
</div>
<script type="text/javascript">
$$('.core_main_ynsocialads').getParent().addClass('active');
</script>