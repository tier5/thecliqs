<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynsocialads/externals/scripts/moo.flot.js');
?>
<script type="text/javascript">
function changeOrder(listby, default_direction)
{
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
}

function actionSelected(actionType)
{
    var checkboxes = $$('td.ynsocialads_check input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'ynsocialads/ads/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
}
function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<br />

<p>
	<?php echo $this->translate("YNSOCIALADS_MY_AD_DESCRIPTION") ?>
</p>

<br />
<div class="yn_filter">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php $ad_ids = "";
if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<div class="fixed-scrolling">
<table class='admin_table frontend_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.name', 'DESC');"><?php echo $this->translate("Name") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.status', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('campaign.title', 'DESC');"><?php echo $this->translate("Campaign") ?></a></th>										
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.click_count', 'DESC');"><?php echo $this->translate("Clicks") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.impressions_count', 'DESC');"><?php echo $this->translate("Impressions") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.unique_click_count', 'DESC');"><?php echo $this->translate("Unique Clicks") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.reaches_count', 'DESC');"><?php echo $this->translate("Reaches") ?></a></th>
      <th><?php echo $this->translate("Remaining") ?></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.ad_type', 'DESC');"><?php echo $this->translate("Type") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): 
		$show_date = 0;
		$ad_ids .= $item -> getIdentity(). ",";
		if(!empty($item->start_date) && !empty($item->end_date))
		{
	    	$startDateObject = new Zend_Date(strtotime($item->start_date));
			$endDateObject = new Zend_Date(strtotime($item->end_date));
			if( $this->viewer() && $this->viewer()->getIdentity() ) 
			{
				$tz = $this->viewer()->timezone;
				$startDateObject->setTimezone($tz);
				$endDateObject->setTimezone($tz);  
			}
			$show_date = 1;
		}
		else {
			$show_date = 0;
		}
    	?>
      <tr>
        <td class="ynsocialads_check"><input type='checkbox' class='checkbox' value='<?php echo $item->getIdentity() ?>' /></td>
        <td><?php echo $this->htmlLink($item->getHref(), $this->translate($item->name), array()) ?></td>
        <td><?php echo ucfirst($this->translate($item->status))?></td>
		<td><?php echo $this->translate($item->getCampaignName()) ?></td>
		<td><?php if($show_date) echo $this->locale()->toDate($startDateObject)?></td>
		<td><?php if($show_date) echo $this->locale()->toDate($endDateObject) ?></td>
		<td><?php echo $item->click_count ?></td>
		<td><?php echo $item->impressions_count ?></td>
		<td><?php echo $item->unique_click_count ?></td>
		<td><?php echo $item->reaches_count ?></td> 
		<td>
			<?php echo $this -> translate(array("%s ".$item->getPackage()->benefit_type, "%s ".$item->getPackage()->benefit_type."s", $item->getRemain()), $item->getRemain());?>
		</td>
		<td><?php echo $this->translate($item->ad_type) ?></td>
        <td>
          <!-- delete --> 
          <?php if($item->status != "deleted"):?>  
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_extended', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Delete', 'id' => $item->getIdentity()),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>
           <!-- edit --> 
          <?php if($item->status == "draft" || $item->status == "unpaid") :?>
          <?php if($item->isEditable() && (!$item->isPayLater())) :?>    
          |  	
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_extended', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'edit', 'id' => $item->getIdentity()),
                $this->translate("edit"),
                array()) ?>
          <?php endif;?>
          |      
          <?php if($item->getPackage()->price != 0) :?>          
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'place-order', 'id' => $item->getIdentity()),
                $this->translate("place order"),
                array()) ?>
          <?php else: ?>
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Publish', 'id' => $item->getIdentity()),
                $this->translate("publish"),
                array('class' => 'smoothbox')) ?>    
          <?php endif;?>
          <?php endif;?>
          <!-- pause -->  
           <?php if($item->status == "running"):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_extended', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Pause', 'id' => $item->getIdentity()),
                $this->translate("pause"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>      
          <!-- resume -->  
           <?php if($item->status == "paused"):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_extended', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Resume', 'id' => $item->getIdentity()),
                $this->translate("resume"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>            
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<br />
<div class='buttons'>
  <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
</div>
</form>
<br/>
  <form id='action_selected' method="post" action="">
   		<input type="hidden" id="ids" name="ids" value=""/>
  </form>
<div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
 </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no ads yet.") ?>
    </span>
  </div>
<?php endif; ?>

<div class="yn_filter">
    <?php echo $this->formStatistic->render($this) ?>
</div>

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

      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-data')) ?>');
      url.setData(args);
      new Request.JSON({
			method: 'post',
			url: url,
			data: {
				'ad_ids': '<?php echo $ad_ids ?>'
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
    <div id="placeholder" style="width:800px;height:350px;"></div>
  	<div id="clickInfo"></div> 
  </div>
</div>
