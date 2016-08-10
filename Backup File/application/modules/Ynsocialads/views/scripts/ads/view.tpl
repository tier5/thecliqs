<script type="text/javascript">
</script>
<script type="text/javascript" src="<?php $this->baseURL()?>application/modules/Ynsocialads/externals/scripts/moo.flot.js"></script>

<div id="ad_view">
<h3 class="yn_title">
    <span>
    <?php echo $this->htmlLink(
        array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads'),
        $this->translate('My Ads'),
        array()) ?>
    </span>
    <span> >> </span>
    <span>
    <?php echo $this->htmlLink(
        $this->ad->getHref(),
        $this->ad->name,
        array()) ?>
    </span>
</h3>
<form id="order_form">
    
</form>
<div class="fixed-scrolling">
<table class="admin_table frontend_table ynsocial_table">
  <thead>
    <tr>
      <th><?php echo $this->translate('Name') ?></th>
      <th><?php echo $this->translate('Status') ?></th>
      <th><?php echo $this->translate('Campaign') ?></th>
      <th><?php echo $this->translate("Start Date") ?></th>
      <th><?php echo $this->translate("End Date") ?></th>
      <th><?php echo $this->translate("Running Date") ?></th>
      <th><?php echo $this->translate("Clicks") ?></th>
      <th><?php echo $this->translate("Impressions") ?></th>
      <th><?php echo $this->translate("Unique Clicks") ?></th>
      <th><?php echo $this->translate("Reaches") ?></th>
      <th><?php echo $this->translate("Remaining") ?></th>
      <th><?php echo $this->translate("Type") ?></th>
      <th><?php echo $this->translate('Options') ?></th>
    </tr>
  </thead>
  <tbody>
      <tr>
        <td><?php echo $this->htmlLink($this->ad->getHref(), $this->translate($this->ad->name), array()) ?></td>
        <td><?php echo ucfirst($this->translate($this->ad->status)) ?></td>
        <td><?php echo $this->translate($this->ad->getCampaignName()) ?></td>
        <td><?php if($this->ad->start_date) echo $this->locale()->toDate($this->ad->getStartDate())?></td>       
        <td><?php if($this->ad->end_date) echo $this->locale()->toDate($this->ad->getEndDate())?></td>
        <td><?php if($this->ad->running_date) echo $this->locale()->toDate($this->ad->getRunningDate())?></td>
        <td><?php echo $this->ad->click_count ?></td>
        <td><?php echo $this->ad->impressions_count ?></td>
        <td><?php echo $this->ad->unique_click_count ?></td>
        <td><?php echo $this->ad->reaches_count ?></td>
        <td><?php echo $this->ad->getRemain()." ".$this->ad->getPackage()->benefit_type."s" ?></td>
        <td><?php echo $this->translate($this->ad->ad_type) ?></td>
      
        <td>
            <!-- delete --> 
          <?php if($this->ad->status != "deleted" && $this->ad->isDeletable()):?>  
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Delete', 'id' => $this->ad->ad_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>
           <!-- edit --> 
          <?php if ($this->ad->isEditable()) : ?>
          <?php if($this->ad->status == "draft" || $this->ad->status == "unpaid") :?>
          <?php if(!$this->ad->isPayLater()) :?>            
          |     
          <?php echo $this->htmlLink(
                //TODO
                //add edit link
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'edit', 'id' => $this->ad->ad_id),
                $this->translate("edit"),
                array()) ?>
          <?php endif;?> 
          |
          <?php if($this->ad->getPackage()->price != 0) :?>          
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'place-order', 'id' => $this->ad->ad_id),
                $this->translate("place order"),
                array()) ?>
          <?php else: ?>
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Publish', 'id' => $this->ad->ad_id),
                $this->translate("publish"),
                array('class' => 'smoothbox')) ?>    
          <?php endif;?>
          <?php endif;?>  
          <!-- pause -->  
           <?php if($this->ad->status == "running"):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Pause', 'id' => $this->ad->ad_id),
                $this->translate('pause'),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>      
          <!-- resume -->  
           <?php if($this->ad->status == 'paused'):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Resume', 'id' => $this->ad->ad_id),
                $this->translate('resume'),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>
          <?php endif;?>
        </td>
      </tr>
  </tbody>
</table>
</div>
<div id="ad_statistics" class='ynsocial_table'>
    <?php
        if (count($this->data) > 0) { ?>
          <div class="fixed-scrolling">
            <table class="ynsocial_table">
                <p class="yn_title" id="statistic_title"><?php echo $this -> translate("Statistics"); ?></p>
                <caption style="margin-bottom: 10px">
                    <a class="buttonlink view_full_report_icon" href="socialads/report?campaign_id=<?php echo($this->ad->campaign_id)?>&ad_id=<?php echo($this->ad->ad_id)?>">
                    <?php echo $this->translate('View Full Report')?>
                    </a>
                 </caption>
            <thead>
            <tr>
                <th><?php echo $this->translate('Date')?></th>
                <th><?php echo $this->translate('Impressions')?></th>
                <th><?php echo $this->translate('Clicks')?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->data as $item): ?>
            <?php $ad = Engine_Api::_()->getItem('ynsocialads_ad', $item['ad_id']);?>
            <tr>
                <td>
                <?php
                    $date =  new Zend_Date(strtotime($item['date']));
                    echo ($this->locale()->toDate($date->setTimeZone($this->timezone)));
                    ?>
                </td>
                <td><?php echo $item['impressions'] ?></td>
                <td><?php echo $item['clicks'] ?></td>
            </tr>
            <?php endforeach; ?>

            </tbody>
          </table>
        </div>
     <?php } ?>
</div>
<div id="preview_ad">
    <div class="ynsocial_ads" style="width: 300px;" >
        <div class="ynsocial_ads_title">
            <?php if ($this->viewer()->getIdentity()): ?>
                <a href="<?php echo $this->url(array('action'=>'create-choose-package'), 'ynsocialads_ads',true)?>"><?php echo $this->translate('Create Ads'); ?></a>
            <?php endif;?>
            <h5><?php echo $this->translate('Ads'); ?></h5>
        </div>
        <div class="ynsocial_ads_content">
            <div class="ynsocial_ads_item">
                <span onclick="javascript:clickSetting(this);" class="ynsocial_ads_setting" id="ynsocial_ads_<?php echo $this->content_id.'_'.$this->ad->ad_id ?>" data-id="ynsocial_ads_setting_<?php echo $this->content_id.'_'.$this->ad->ad_id; ?>">
                </span>
    
                <div class="ynsocial_ads_setting_choose" id="ynsocial_ads_setting_<?php echo $this->content_id.'_'.$this->ad->ad_id; ?>">   
                    <a onclick="javascript:hideAd(this); return false;" ad_id= '<?php echo $this->ad->getIdentity(); ?>'  href='#'>
                        <?php echo  $this->translate('Hide this ad'); ?>
                    </a> 
                    <a onclick="javascript:hideOwner(this); return false;" ad_id= '<?php echo $this->ad->getIdentity(); ?>'  href='#'>
                        <?php echo  $this->translate('Hide all ads from this advertiser'); ?>
                    </a> 
                </div>
    
                <a ad_id='<?php echo $this->ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $this->ad->getIdentity(); ?> ynsocial_ads_cont_title" href="<?php echo $this->ad->getLinkUpdateStats()?>/preview/1">
                <?php echo $this->translate($this->ad->name);?>
                </a>
                <a ad_id='<?php echo $this->ad->getIdentity(); ?>' onclick="preventClick(this,event);" class="prevent_click_<?php echo $this->ad->getIdentity(); ?> ynsocial_ads_cont_image" href="<?php echo $this->ad->getLinkUpdateStats()?>/preview/1">
                    <img src="<?php echo $this->ad -> getPhotoUrl('thumb.normal') ?>"/>
                </a>
                <div class="ynsocial_ads_cont"><?php echo $this->translate($this->ad->description)?></div>
                
                <?php if ($this->viewer()->getIdentity()): ?>
                        <?php if ($this->ad->likes()->isLike($this->viewer())) : ?>
                            <span class="icon_ynsocial_ads_like"></span>        
                            <a ad_id= '<?php echo $this->ad->getIdentity(); ?>' title="<?php echo $this->translate("Unlike")?>"
                            id="ynsocialads_unlike" href="javascript:void(0);"
                            onClick="ynsocialads_like(this);"
                            class= 'ynsocialads_unlike'> 
                                 <?php echo $this->translate("Unlike")?>
                            </a>    
                        <?php else : ?>
                            <span class="icon_ynsocial_ads_like"></span>
                            <a ad_id= '<?php echo $this->ad->getIdentity(); ?>' title="<?php echo $this->translate("Like") ?>" id="ynsocialads_like"
                                    href="javascript:void(0);" onClick="ynsocialads_like(this);"
                                    class= 'ynsocialads_like'> 
                                <?php echo $this->translate("Like")?>
                            </a>
                    <?php endif;?>
                <?php endif; ?>
                
                <?php
                    $isLike = 0; if ($this->ad->likes()->isLike($this->viewer())) $isLike = 1;
                    $aUserLike = $this->ad->getUserLike();
                    $likes = $this->ad->likes()->getAllLikesUsers();
                ?>
                <div id='count_like_<?php echo $this->ad->getIdentity(); ?>' <?php if((count($likes) < 1) && !$isLike && (count($aUserLike) < 1)) echo "class=''"; else echo "class='ynsocial_ads_like_cont'"; ?>>
                
                <div id='display_name_like_<?php echo $this->ad->getIdentity(); ?>' style="display: <?php if($isLike) echo 'inline'; else echo 'none';?>">
                    <a href="<?php echo $this->viewer()->getHref();?>"><?php echo $this->translate('You'); ?></a>
                </div>  
                <?php
                    //handle like function
                    $return_str = "";
                    if(count($aUserLike) > 0){
                        $iUserId = $aUserLike[0]['iUserId'];
                        $user = Engine_Api::_() -> getItem('user', $iUserId);
                        $sDisplayName = $aUserLike[0]['sDisplayName'];
                        $return_str = "<a href='".$user->getHref()."'>".$sDisplayName."</a>";
                        if($isLike)
						{
							if(count($likes) > 2)
							{
								$return_str = ", " . $return_str . $this -> translate(array(" and %s other liked this.", " and %s others liked this." ,count($likes) -1), count($likes) -1);	
							}
							else 
							{
								$return_str = ", ". $return_str . $this -> translate(' liked this.');
							}
						}
						else {
							if(count($likes) > 1)
							{
								$return_str = $return_str. $this -> translate(array(" and %s other liked this."," and %s others liked this.", count($likes)), count($likes));
							}
							else 
							{
								$return_str = $return_str . $this -> translate(' liked this.');
							}
						}
                    }
                    else 
                    {
                        if($isLike)
						{
							if(count($likes) > 1)
							{
								$return_str .= $this -> translate(array("and %s other liked this.", "and %s others liked this.", count($likes) -1), count($likes) -1); 
							}
							else 
							{
								$return_str .= $this -> translate(' liked this.');
							}
						}
						else {
							if(count($likes) > 0)
							{
								$return_str .= count($likes). $this -> translate(' people liked this.');
							}
						}
                    }
                    //end function
                ?>
                <div style='display: inline' id='ajax_call_<?php echo $this->ad->getIdentity(); ?>'><?php echo $return_str;?></div>
                </div>
            </div>  
        </div>
    </div>
</div>
<div id="placement">
    
</div>
<?php if ($this->viewer->getIdentity() == $this->ad->user_id) : ?>
<div id="create_similar_ad" class="add_link">
    <?php echo $this->htmlLink(
        array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'create-similar', 'id' => $this->ad->ad_id),
        $this->translate('Create a Similar Ad'),
        array('class' => 'smoothbox buttonlink create_ad_icon')) ?> 
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
<div class="admin_statistics">
  <script type="text/javascript" src="<?php echo $this->layout()->staticBaseUrl ?>externals/swfobject/swfobject.js"></script>
  <script type="text/javascript">
    
    var preventClick = function(obj,event){
        var ad_id = obj.getProperty('ad_id');
        var prevent_click = '.prevent_click_'+ad_id;
        $$(prevent_click).addClass('click_disabled');
    }
    
    function ynsocialads_like(ele)     
{   
    var ad_id = ele.getProperty('ad_id');
    ele.setStyle('display', 'none');
    if (ele.className=="ynsocialads_like") {
        var request_url = '<?php echo $this->url(array('module' => 'ynsocialads', 'controller' => 'like', 'action' => 'like'), 'default', true); ?>';
    } else {
        var request_url = '<?php echo $this->url(array('module' => 'ynsocialads', 'controller' => 'like', 'action' => 'unlike'), 'default', true); ?>';
    }
    request_url = request_url + '/subject/ynsocialads_ad_'+ad_id;
    new Request.JSON({
        url:request_url ,
        method: 'post',
        data : {
            format: 'json',
            'type':'ynsocialads_ad',
            'id': ad_id
        },
        onComplete: function(responseJSON, responseText) {
            ele.setStyle('display', 'inline');
            if (responseJSON.error) {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            } else {
                 if (ele.className=="ynsocialads_like") {
                    ele.setAttribute("class", "ynsocialads_unlike")|| ele.setAttribute("className", "ynsocialads_unlike");
                    ele.title= '<?php echo $this->translate("Unlike") ?>';
                    ele.innerHTML = '<?php echo $this->translate("Unlike") ?></i>';
                    var class_name = 'display_name_like_' + ad_id;
                    var ajax_class = 'ajax_call_' +  ad_id;
                    var count_class = 'count_like_' + ad_id;
                    $(class_name).setStyle('display', 'inline');
                    $(ajax_class).set('html', responseJSON['list']);
                    $(count_class).addClass('ynsocial_ads_like_cont');
                } else {    
                    ele.setAttribute("class", "ynsocialads_like")|| ele.setAttribute("className", "ynsocialads_like"); 
                    ele.title= '<?php echo $this->translate("Like") ?>';                        
                    ele.innerHTML = '<?php echo $this->translate("Like") ?>';
                    var class_name = 'display_name_like_' + ad_id;
                    var ajax_class = 'ajax_call_' +  ad_id;
                    if(responseJSON['count'] < 1)
                    {
                        var count_class = 'count_like_' + ad_id;
                         $(count_class).removeClass('ynsocial_ads_like_cont');
                    }
                    $(class_name).setStyle('display', 'none');
                    $(ajax_class).set('html', responseJSON['list']);
                }                    
            }
        }
    }).send();
}
    
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
      
      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('action' => 'chart-ad','id'=>$this->ad->ad_id)) ?>');
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
              //      console.log(event, pos, items);
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
</div>
<script type="text/javascript">
$$('.core_main_ynsocialads').getParent().addClass('active');
</script>