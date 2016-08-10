<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynlistings/externals/scripts/moo.flot.js');
?>
<h2>
  <?php echo $this->translate('Statistics') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>
<p><?php echo $this->translate("YNLISTINGS_ADMIN_STATISTICS_DESCRIPTION") ?></p>
<h4><?php echo $this->translate("General Statistics") ?></h4>
<div class='clear'>
  <div class='settings'>
	      <table class='admin_table'>
	        <tbody>
	         	<tr>
	            	<td><?php echo $this -> translate('Total Listings:') ?></td>
	            	<td><?php echo $this -> total_listings ;?></td>
	            </tr>
	            <tr>   
	           	 	<td><?php echo $this -> translate('Total Published Listings:') ?></td>
	           		<td><?php echo $this -> published_listings ;?></td>
	            </tr>
	            <tr> 
	           	 	<td><?php echo $this -> translate('Total Listings in Draft:') ?></td>
	           		<td><?php echo $this -> draft_listings ;?></td>
	            </tr>
	            <tr> 
	           	    <td><?php echo $this -> translate('Total Closed Listings:') ?></td>
	            	<td><?php echo $this -> close_listings ;?></td>
	            </tr>
	            <tr> 
	            	<td><?php echo $this -> translate('Total Open Listings:') ?></td>
	            	<td><?php echo $this -> open_listings ;?></td>
	            </tr>
	            <tr> 
	            	<td><?php echo $this -> translate('Total Approved Listings:') ?></td>
	            	<td><?php echo $this -> approved_listings ;?></td>
	            </tr>
	            <tr> 
	           	 	<td><?php echo $this -> translate('Total Disapproved Listings:') ?></td>
	           	 	<td><?php echo $this -> disapproved_listings ;?></td>
	            </tr>
	            <tr> 
	           	 	<td><?php echo $this -> translate('Total Featured Listings:') ?></td>
	           	 	<td><?php echo $this -> feature_listings ;?></td>
	            </tr>
	            <tr> 
	          	  	<td><?php echo $this -> translate('Total Reviews:') ?></td>
	           	 	<td>0</td>
	            </tr>
	            <tr> 
	           	 	<td><?php echo $this -> translate('Total Discussions:') ?></td>
	           	 	<td><?php echo $this -> topic_count;?></td>
	            </tr>
	            <tr> 
	           	 	<td><?php echo $this -> translate('Total Discussions Posts:') ?></td>
	           	 	<td><?php echo $this -> post_count;?></td>
	            </tr>
	            <tr> 
	            	<td><?php echo $this -> translate('Total Photos in Listings:') ?></td>
	            	<td><?php echo $this -> photo_count ;?></td>
	            </tr>
	            <tr> 
	            	<td><?php echo $this -> translate('Total Videos in Listings:') ?></td>
	            	<td><?php echo $this -> video_count; ?></td>
	            </tr>
	        </tbody>
	      </table>
  </div>
</div>
<h4><?php echo $this->translate("Graph Statistics") ?></h4>
<p><?php echo $this->translate('The filters below allow you to observe various metrics and their change over different time periods.')?></p>
<div class="admin_search">
  	<div class="search">
    	<?php echo $this->formChartStatistic->render($this) ?>
	</div>
</div>
<br />
<div class="admin_statistics_nav">
    <a href="" id="admin_stats_offset_previous" onclick="processStatisticsPage(-1, event);"><?php echo $this->translate("Previous") ?></a>
    <a href="" id="admin_stats_offset_next" onclick="processStatisticsPage(1, event);" style="display: none;"><?php echo $this->translate("Next") ?></a>
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
            if( ['dd', 'ww','MM'].indexOf(children[i].get('value')) == -1 ) {
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

      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('module'=>'ynlistings', 'controller'=>'statistics', 'action' => 'chart-data'),'admin_default') ?>');
      url.setData(args);
      new Request.JSON({
			method: 'post',
			url: url,
			data: {
			},
			onSuccess: function(responseJSON) 
			{
				var tooltip = new Element('div', {
				    id: "tooltip"
				});
				var json_data = responseJSON.json;
				var d = [];
                var ticks = [];
                var count = 0;
                
                for(var i in json_data)
                {
                    d.push([count, json_data [i]]);
                    ticks.push([count, i]);
                    count = count +1;
                }   
                
                var data = [];
                switch(args.type) {
                    case "listings":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Listings")?>'
		                }];
                        break;
                    case "photos":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Photos")?>'
		                }];
                        break;    
                    case "reviews_listings":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Reviews")?>'
		                }];
                        break;
                    case "videos":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Videos")?>'
		                }];
		                 break;
		             case "discussions":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Discussions")?>'
		                }];
		                 break;
		             case "discussions_posts":
                         var data = [{
		                    data: d,
		                    label: '<?php echo $this -> translate("Discussions Posts")?>'
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
      	'type' : 'listings',
        'mode' : 'normal',
        'chunk' : 'dd',
        'period' : 'ww',
        'start' : 0,
        'offset' : 0
      });
      
      
    });
</script>
    <div id="placeholder" style="width:800px;height:350px;position: relative;"></div>
  	<div id="clickInfo"></div> 
</div>
