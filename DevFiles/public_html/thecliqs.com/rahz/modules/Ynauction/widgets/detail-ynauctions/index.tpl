 <?php  $this->locale()->setLocale("en_US"); ?>
<?php
$this->headLink()
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ynauction/externals/styles/prettyPhoto.css');
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/jquery-1.4.4.min.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/jquery.prettyPhoto.js');
$data = $this->product;
$user_id = $this->user_id;
if(!function_exists("selfURLF"))
 {
    function selfURLF() {
         $server_array = explode("/", $_SERVER['PHP_SELF']);
          $server_array_mod = array_pop($server_array);
          if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
          $server_info = implode("/", $server_array);
          return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
     } 
 } 
 $this->locale()->setLocale("en_US");    
?>
<h2 style="padding-left: 10px; color:#000000;">
    <?php echo $this->translate('%1$s\'s auction', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  </h2> 
  
<?php $now = date('Y-m-d H:i:s');
if((($data->display_home != 0 &&  $data->approved == 1) || $data->user_id == $user_id) && $data->is_delete == 0): ?>
    <div class='ynauction_layout_left'>
        <div class="gallery clearfix">
        <!-- main photo    -->
         <?php if($data->getPhotoUrl() != ""): ?>  
                <div class="ynauction_ga_large_photo"><a href="<?php echo $this->product->getPhotoUrl()?>" rel="prettyPhoto[gallery2]" title="<?php echo $this->product->title?>"><img src="<?php echo $this->product->getPhotoUrl()?>" /></a></div>    
                <div class="ynauction_ga_thumb_photo">
                <?php $count = 0;
                foreach($this->paginator as $photo ): $count ++?>
                    <?php if($data->photo_id != $photo->file_id):?>
                    <span class="detaillevel" <?php if($count > 8): ?> style = "display:none;"><?php endif; ?>>
                    <a href="<?php echo $photo->getPhotoUrl()?>" rel="prettyPhoto[gallery2]" title="<?php echo $photo->image_title?>">
                    <img src="<?php echo $photo->getPhotoUrl('thumb.normal')?>"/>
                    </a>
                    </span>
                    <?php endif; ?>
                  <?php endforeach;?>
               </div> 
    
        <?php else: ?>
            <div style="text-align: center"><img src="./application/modules/Ynauction/externals/images/nophoto_product_thumb_profile.png" title="<?php echo $this->product->title?>" style = "max-width:370px;max-height:300px" /></div>                                                                                                              
        <?php endif; ?>
        </div>
        <br/>
    </div> 
    <div class='ynauction_layout_middle' style="float: left; width:378px ;">
    <table width="100%">
    <tr>
     <td valign='top' class="contentbox" style="width: auto; padding-bottom : 10px;">
            <span id="title" style="font-size: 14pt; font-weight: bold;">
                    <?php echo $this->htmlLink($data->getHref(), $data->getTitle()) ?>
                </span>
                <div class="ynauctions_browse_date">              
                    <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($data->getOwner()->getHref(), $data->getOwner()->getTitle()) ?>
                    <?php echo $this->timestamp(strtotime($data->creation_date))?> 
                </div>
                <div class="ynauctions_browse_date">
                     <?php echo $this->translate('Bid history');?>:  
                     <a href="javascript:;"><span id="total"> <?php echo $data->total_bids; ?></span> 
                     <?php if($data->total_bids == 1): echo $this->translate('Bid'); else: echo $this->translate('Bids'); endif;?>  </a>
                      | 
                      <?php echo $this->translate('Latest Bidder');?>:  
                     <span class="biddername" style="background-image: none; background-color: transparent;">
                     <span class="lastbidder" id="username">
                     <?php if($data->bider_id >= 0):
                     $bider = Engine_Api::_()->getItem('user', $data->bider_id);
                     if($bider->getIdentity() > 0): ?>
                     <a href="<?php echo $bider->getHref(); ?>">
                      <?php  echo $bider->getTitle();  ?> </a>
                       <?php  else:
                        echo $this->translate('Nobody');
                     endif;
                     else:
                        echo $this->translate('User have deleted');
                     endif;
                     ?>
                     </span></span>
                 </div> 
                 
                 <div class="ynclockcont" id="clockcont">
                     <div class="label" id="label">
                        <?php echo $this->translate('Deal End In');?>
                     </div>
                     <div class="clock">
                         <span class="secactive" id = "secactive"> 
                         <?php if(($data->status != 0 || $data->end_time <= $now || $data->status == 3) && ($data->status != 1)): 
                         if($data->bider_id == 0)
                            $data->status = 3; 
                         else {
                            $data->status = 1;
						 	
							$winner = Engine_Api::_() -> getItem('user', $data -> bider_id);
							//send notify
							//Send sell
							$productOwner = $data -> getOwner();
							$notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
							if ($data -> user_id != $user_id)
							{
								$notifyApi -> addNotification($productOwner, $winner, $data, 'ynauction_won', array('label' => 'Auction'));
							}
							//send users
							$userBids = Engine_Api::_() -> ynauction() -> getUserBid($data -> product_id, $data -> user_id);
							foreach ($userBids as $bid)
							{
								if ($bid -> ynauction_user_id != $winner -> getIdentity() && $bid -> ynauction_user_id != $productOwner -> getIdentity())
								{
									$userBid = Engine_Api::_() -> getItem('user', $bid -> ynauction_user_id);
									$notifyApi -> addNotification($userBid, $winner, $data, 'ynauction_won_bidded', array('label' => $data -> title));
								}
							}
						 } 
                         $data->stop = 1;
                         $data->save();?>
                         <?php endif;?>
                         <?php if($data->stop == 1 && $data->status == 0 && $data->display_home == 1):?>
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate('Stopped'); ?></font>
                             <?php elseif($data->status == 0 && $data->display_home == 0): ?>
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Created") ?>  </font>  
                             <?php elseif($data->status == 0 && $data->display_home == 1 && $data->approved == 0): ?> 
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Pending") ?>   </font>  
                             <?php elseif($data->status == 0 && $data->display_home == 1 && $data->start_time > $now): ?> 
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Upcoming") ?></font>  
                             <?php elseif($data->status == 0 && $data->display_home == 1 && $data->stop == 0): ?>
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Running") ?> </font>  
                             <?php elseif($data->status == 1): ?>
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Won") ?>  </font>  
                              <?php elseif($data->status == 2): ?>
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Paid") ?>  </font>  
                              <?php elseif($data->status == 3): ?>
                             <font color="silver" style="font-weight: bold;"><?php echo $this->translate("Ended") ?>  </font>  
                             <?php endif; ?>
                         </span>
                     </div>
                 </div>
                 <?php if($data->status == 0): ?>
                 <div class="ynprice">
                    <div class="group_price">
                        <span class="label"><?php echo $this->translate('Starting Price');?></span>  <br/>
                        <span class="price"> <?php echo $this->locale()->toCurrency($data->starting_bidprice,$data->currency_symbol); ?></span>
                    </div>
                    <div class="group_price">
                        <span class="label"><?php echo $this->translate('BuyOut Price');?></span>  <br/>
                        <span class="price"> <?php 
                        if($data->price != 0)
                            echo $this->locale()->toCurrency($data->price,$data->currency_symbol); 
                        else
                            echo $this->translate("N/A");?></span>
                    </div>
                    <div class="group_price">
                        <span class="label"><?php echo $this->translate('Current Price');?></span>  <br/>
                        <span class="price" id="pricecur0"> 
                              <?php 
                                  if($data->bid_price < $data->starting_bidprice): 
                                        $price = $data->starting_bidprice;
                                  else: $price = $data->bid_price; 
                                  endif;
                              echo $this->locale()->toCurrency($price,$data->currency_symbol);
                              ?>
                        </span>
                    </div>
                 </div>
                 <br/>
                 <div class="baselevel_detail">
                     <div class="max_bid">
                        <?php echo $this->translate('Your Max Bid');?>
                     </div>
                     <div style="float: left; padding-right: 0px;"> 
                     <input class="input" type="text" id="max_bid" onkeypress="return onlyNumbers(event);" value=""/>
                     </div> 
                     <div class="btnwinner" id = "ended">
                         <div class="btn" id = "btn">
                             <?php if($user_id > 0):
                                $block =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.block', 1);
                                $product = Engine_Api::_()->getItem('ynauction_product', $data->product_id);  
								
								$latestCanBid = (!(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.latestbid', 0)) && ($product->bider_id != 0)) ? ($user_id != $product->bider_id) : true;
								
                                $info_account = Ynauction_Api_Account::getCurrentAccount($user_id);     
                                $privacy = Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams($data, null, 'view')->checkRequire();
                                if(($product->user_id == $user_id || ip2long($_SERVER['REMOTE_ADDR']) == $data->creation_ip) && $privacy == 1 ):
                                    if($data->status == 0):
                                        if($block == 0 || $data->stop == 1 || $data->display_home == 0 || $data->approved == 0 || $data->start_time > $now || (!$latestCanBid)): ?> 
                                            <div class="active" id="bid0" style="background-position: 0 -193px"> <?php echo $this->translate('Place Bid');?></div>      
                                        <?php else: ?>
                                            <div class="active" id="bid0" style="background-position: 0 -96px"><a href="javascript:;" onclick="return bidFeatured(<?php echo $data->product_id?>);"> <?php echo $this->translate('Place Bid');?></a></div>  
                                        <?php endif; ?>
                                    <?php elseif($data->status == 1): ?>  
                                        <div class="active" id="bid0" style="background-position: 0 -128px"><?php echo $this->translate('Won');?></div>  
                                    <?php elseif($data->status == 2): ?> 
                                        <div class="active" id="bid0" style="background-position: 0 -224px"><?php echo $this->translate('Sold');?></div>  
                                    <?php endif; ?>
                                    <?php elseif(($data->stop == 1 && $data->status == 0) || $privacy == 0): ?> 
                                        <div class="active" id="bid0" style="background-position: 0 0px"> <?php echo $this->translate('Place Bid');?></div>  
                                    <?php elseif($data->status == 0 ):?>
                                    <div class="active" id="bid0" style="background-position: 0 -96px"><a href="javascript:;" onclick="return bidFeatured(<?php echo $data->product_id?>);"> <?php echo $this->translate('Place Bid');?></a></div>  
                                    <?php elseif($data->status == 1): ?>  
                                        <div class="active" id="bid0" style="background-position: 0 -128px"><?php echo $this->translate('Won');?></div>  
                                    <?php elseif($data->status == 2): ?> 
                                        <div class="active" id="bid0" style="background-position: 0 -224px"><?php echo $this->translate('Sold');?></div>  
                                    <?php endif;
                                else:
                                    if(($data->stop == 1 && $data->status == 0) || $data->status == 3 || $data->display_home == 0 || $data->approved == 0 || $data->start_time > $now): ?> 
                                    <div class="active" id="bid0" style="background-position: 0 -193px"> <?php echo $this->translate('Place Bid');?></div>      
                                    <?php elseif($data->status == 0): ?>
                                        <div class="active" id="bid0"> <a href="login" onmouseover="this.innerHTML = 'Login'" onmouseout="this.innerHTML = 'Place Bid'"><?php echo $this->translate('Place Bid');?></a></div>
                                    <?php elseif($data->status == 1): ?>  
                                        <div class="active" id="bid0" style="background-position: 0 -128px"><?php echo $this->translate('Won');?></div>  
                                    <?php elseif($data->status == 2): ?> 
                                        <div class="active" id="bid0" style="background-position: 0 -224px"><?php echo $this->translate('Sold');?></div>  
                                    <?php endif;?>
                                <?php endif; ?>
                         </div>
                     </div>
                 </div>
                 <div style="padding-left: 100px; padding-top: 5px;" class="ynaution_span">
                     <?php echo $this->translate("(Enter "); ?>
                     <span id = "min_incre">
                     <?php echo $this->locale()->toCurrency($data->bid_price + $data->minimum_increment,$data->currency_symbol); ?>
                     </span> 
                    <?php echo $this->translate(" ");    ?>
                     <span id = "max_incre">
                     <?php if($data->maximum_increment <= 0) {
                        echo $this->translate("or "); 
                        echo $this->translate("more");
                     }
                     else
                     { 
                        echo $this->translate("or up to ");
                        echo $this->locale()->toCurrency($data->bid_price + $data->maximum_increment,$data->currency_symbol); 
                     }?>
                     </span>)
                     
                 </div>
                 <?php endif; ?>
                 <?php if($data->user_id != $user_id) :?>
	                 <input type="hidden" id="confirm_user" value="<?php if(Engine_Api::_()->ynauction()->checkConfirm($user_id)){ echo "1";} else{ echo "0";}?>"> 
	                     <?php if($user_id > 0 && $data->status == 0 && $data->price != 0 && $data->end_time > $now && $data->is_delete == 0 && $data->display_home == 1 && $data->approved == 1 && $data->stop == 0 && $data->start_time < $now): ?> 
	                     <div id='buynow_btn' style= "padding-top: 15px; padding-left: 100px; float: left;">   
	                        <span class="buynow_btn">
	                       <?php  echo $this->htmlLink(array(
	                              'action' => 'buynow',
	                              'auction' => $data->product_id,
	                                'route' => 'ynauction_general',
	                            ), $this->translate('Buy Now'), array(
	                            ));  ?>
	                       </span> 
	                        
	                     </div>
	                 <?php endif;    ?> 
	                 <?php if($user_id > 0 && $data->status == 0 && $data->proposal == 0 && $data->end_time > $now && $data->is_delete == 0 && $data->display_home == 1 && $data->approved == 1 && $data->stop == 0 && $data->start_time < $now): ?>
	                     <div id = 'proposal_btn' style= "padding-top: 15px; padding-left: 100px;">   
	                        <span class="proposal_btn">
	                       <?php  echo $this->htmlLink(array(
	                              'action' => 'proposal-price',
	                              'auction' => $data->product_id,
	                                'route' => 'ynauction_proposal',
	                            ), $this->translate('Proposal Price'), array( 'class' => 'smoothbox',
	                            ));  ?>
	                       </span> 
	                     </div>
                 		<?php endif;    ?>
                 	<?php endif;    ?>	
           </td>
    </tr>
    </table>
</div>
<script type="text/javascript" charset="utf-8">
jQuery.noConflict();
jQuery(document).ready(function(){
    jQuery("area[rel^='prettyPhoto']").prettyPhoto();
        
    jQuery(".gallery:first a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'facebook',slideshow:5000, autoplay_slideshow: true});
    jQuery(".gallery:gt(0) a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, hideflash: true});

    jQuery("#custom_content a[rel^='prettyPhoto']:first").prettyPhoto({
            custom_markup: '<div id="map_canvas" style="width:260px; height:265px"></div>',
            changepicturecallback: function(){ initialize(); }
        });

    jQuery("#custom_content a[rel^='prettyPhoto']:last").prettyPhoto({
            custom_markup: '<div id="bsap_1259344" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div><div id="bsap_1237859" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6" style="height:260px"></div><div id="bsap_1251710" class="bsarocks bsap_d49a0984d0f377271ccbf01a33f2b6d6"></div>',
            changepicturecallback: function(){ _bsap.exec(); }
        });
    });
</script>
<?php  if($data->status == 0 && $data->end_time > $now && $data->is_delete == 0 && $data->display_home == 1 && $data->approved == 1 && $data->start_time < $now):?>
<script language="javascript">
    <?php 
     if($data->bid_price < $data->starting_bidprice): 
      $price = $data->starting_bidprice;
      else: $price = $data->bid_price; 
      endif; ?>
var pricecur0 = '<?php echo $this->locale()->toCurrency($price,$data->currency_symbol) ?> ';
var cdFeatured = null;
<?php 
  $time = strtotime($data->end_time) - time();
  $min =  floor($time/60);
  $sec = $time%60;
        ?>
var mins = <?php echo  $min?>;
var secs =  <?php echo  $sec?>; 
var flag = false; 
var flagc = false;
var flagStart = 1;
var total_bids = <?php echo $data->total_bids?>; 
var tempf = "";
var f;
var latestCanBid = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.latestbid', 0)?>;
var bid_increment = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.increment', 1) ?>;  
var flag_updateF = true; 
var minbid = <?php echo $data->bid_price + $data->minimum_increment; ?>; 
if(total_bids <= 0)
{
    minbid = <?php echo $data->starting_bidprice; ?>; 
}
var maxbid = <?php echo $data->bid_price + $data->maximum_increment; ?>;
var min_incre = <?php echo $data->minimum_increment; ?>; 
var max_incre = <?php echo $data->maximum_increment; ?>;
function onlyNumbers(evt) 
{
    var e = evt;
    if(window.event){ // IE
        var charCode = e.keyCode;
    } else if (e.which) { // Safari 4, Firefox 3.0.4
        var charCode = e.which
    }
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    var max_bid = $('max_bid').value;
    
    return true;
}

function bidFeatured(auction_id)
{
    var confirm_user = document.getElementById('confirm_user').value;   
    if(confirm_user == 0)
    {
        var url = '<?php echo $this->url (array('action'=> 'confirm', 'user_id'=>$user_id, 'format' => 'smoothbox', 'auction_id'=> $data->product_id),"ynauction_general")?>';
        Smoothbox.open(url);
        return false;
    }
    var max_bid = parseFloat($('max_bid').value);
    if(!$('max_bid').value)
    {
        alert('<?php echo $this->translate('Please enter your max bid!');?>');
        return false;
    }
    if(max_bid < minbid || (max_bid > maxbid && max_incre != 0))
    {
        if(max_incre != 0)
            alert('<?php echo $this->translate("Please enter your max bid between");?>' + ' ' + roundNumber(minbid,2) + ' ' + '<?php echo $this->translate("and");?>' + ' ' + roundNumber(maxbid,2) + "!");
        else
            alert('<?php echo $this->translate("Please enter your max bid should be greater than");?>' + ' ' + roundNumber(minbid,2) + "!");
        return false;
    }
    tempf = $('btn').innerHTML;
    $('btn').innerHTML = '<div class="active"  style="background-position: 0 -193px"><?php echo $this->translate('Place Bid');?></div>' ;
    var price = $('pricecur0').innerHTML;
      var request = new Request.JSON({
            'method' : 'post',
            'url' :  '<?php echo $this->url(array('module' => 'ynauction', 'action' => 'bid'), 'ynauction_general') ?>',
            'data' : {
                'product_id' : auction_id,
                'max_bid' : max_bid           
            },
            'onComplete':function(responseObject)
            {  
                    minbid = max_bid + min_incre;
                    maxbid = max_bid + max_incre;
                    if(typeof(responseObject)=="object" && responseObject != null)
                    {
                            f = setTimeout("viewFBid()",5000);
                    }
                    else
                        f = setTimeout("viewFBid()",5000);
                    $('max_bid').value = ""; 
            }
        });
        request.send();
    if(flagc == false)
        flagStart = 1;
}
function viewFBid()
{
    if(flag == false && latestCanBid == 1)
        $('btn').innerHTML = tempf;
    clearTimeout(f);
}
function updateFeatured() 
{
    if(flag == false && <?php echo $data->stop ?> == 0)
    {
         if(flag_updateF ==  true)
        {
            flag_updateF = false;
            var request = new Request.JSON({
                    'method' : 'post',
                    'url' : 'application/modules/Ynauction/externals/scripts/update.php',
                    'data' : {
                        'product_id' : <?php echo $data->product_id ?>,
                        'flagStart'  : flagStart
                    },
                    'onComplete':function(responseObject)
                    {  
                        if(responseObject != null)
                        {
                           flag_updateF = true;
                           if(responseObject.min != 0 || responseObject.sec != 0)
                           {                          
                              mins = 1 * m(responseObject.min);
                              secs = 0 + s(":"+responseObject.sec);
                           }
                           pricecur0 =  responseObject.price;
                           $('min_incre').innerHTML = responseObject.min_incre + " ";
                           $('max_incre').innerHTML = responseObject.max_incre;
                           minbid = responseObject.min_incre_num;
                           maxbid = responseObject.max_incre_num;
                           if($('username'))
                           {
                               if(responseObject.username != "")
                                    $('username').innerHTML = responseObject.username;
                               else if(responseObject.userDelete && responseObject.userDelete == 1)
                                    $('username').innerHTML = "<?php echo $this->translate('User have deleted'); ?>";
                               else
                                     $('username').innerHTML = "<?php echo $this->translate('Nobody'); ?>";
                           }
                           if($('total'))
                           {
                              $('total').innerHTML = responseObject.bids;     
                           }
                           if($('proposal_btn'))
                           {
                              if(responseObject.proposal == 1) 
                              {
                                  $('proposal_btn').innerHTML = "";
                              }
                           }
                           if(responseObject.flag == "1")
                                flagc = true;
                            if(responseObject.remove == "1")
                                clearTimeout(cdFeatured);
                           if (responseObject.bider_id != "0") {
                           		if (responseObject.bider_id != <?php echo $user_id?>) {
                           			$('btn').innerHTML = '<div class="active" id="bid0" style="background-position: 0 -96px"><a href="javascript:;" onclick="return bidFeatured(<?php echo $data->product_id?>);"> <?php echo $this->translate('Place Bid');?></a></div>';
                           		}		
                           }
                        }
                    }
                });
                request.send();
        }
        clearTimeout(cdFeatured);    
        redoFeatured();
        var ses = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.timeupdate', 5)?>;
        var minises = ses * 1000;
        up = setTimeout("updateFeatured()",minises); 
    }
}

function m(obj) {
     for(var i = 0; i < obj.length; i++) {
          if(obj.substring(i, i + 1) == ":")
          break;
     }
     return(obj.substring(0, i));
}

function s(obj) {
     for(var i = 0; i < obj.length; i++) {
          if(obj.substring(i, i + 1) == ":")
          break;
     }
     return(obj.substring(i + 1, obj.length));
}

function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}

function dis(mins,secs) 
{
     var disp = "";
     if(mins >= 1440)
     {
         var day = Math.floor(mins/1440);
             disp = day + '<?php echo $this->translate("d"). " " ?>';

         mins = mins - day*1440; 
     }
    
     var h = Math.floor(mins/60);
     if(h <= 9)
        disp +=  "0" + h + ":";
     else
         disp += h + ":";
     mins = mins - h*60;
    
     if(mins <= 9) {
          disp += "0";
     } else {
          disp += "";
     }
     disp += mins + ":";
     if(secs <= 9) {
          disp += "0" + secs;
     } else {
          disp += secs;
     }
     return(disp);
}

function roundNumber(num, dec) {
    var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
    return result;
}
function redoFeatured() {
     if($('secactive'))
     {      
             secs--;
              if(secs == -1) {
                   secs = 59;
                   mins--;
              }                
             $('pricecur0').innerHTML = pricecur0;
             if(flagc == true)
             {
                //Time tio start
                $('secactive').innerHTML = "<span style='color:#666666'>"+dis(mins,secs)+"</span>";
             }
             else if(mins == 0 && secs <= 10)
             {
                  $('secactive').innerHTML =  "<span style='color:#CF0000'>"+ dis(mins,secs) + "</span>";   
             }
             else
                $('secactive').innerHTML = "<span style='color:#597E13'>"+dis(mins,secs)+"</span>";
            if((mins == 0) && (secs == 0) && flagc == false) {
                 flag = true; 
                 if($('proposal_btn'))
                 {
                     $('proposal_btn').innerHTML = '';
                 }
                 if($('buynow_btn'))
                 {
                     $('buynow_btn').innerHTML = '';
                 }
                 clearTimeout(cdFeatured);
                 $('btn').innerHTML = '<div class="active" style="background-position: 0 -160px"><?php echo $this->translate('Won');?></div>';
                 $('secactive').innerHTML ='<font color="silver" style="font-weight: bold;"><?php echo $this->translate('Ended'); ?></font>'; 
                 var request0 = new Request.JSON({
                'method' : 'post',
                'url' :  en4.core.baseUrl + 'auction/win',
                'data' : {
                    'product_id' : <?php echo $data->product_id ?>            
                }
                });
                 request0.send();         
             } else if((mins == 0) && (secs == 0) && flagc == true)
             {
                flagc = false;
             }
             else{
                  cdFeatured = setTimeout("redoFeatured()",1000);
              }
     }
}

function init() 
{
  updateFeatured();
}
window.onload = init;    
</script>
<?php endif; ?>
<?php elseif($data->is_delete == 0): ?>
<div class="tip">
      <span>
        <?php echo $this->translate('This auction have not displayed or stopped.');?>
      </span>
</div>
<?php else: ?>
<div class="tip">
      <span>
        <?php echo $this->translate('This auction has been deleted.');?>
      </span>
</div>
<?php endif; ?>
