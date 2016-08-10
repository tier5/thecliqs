<?php  $this->locale()->setLocale("en_US"); ?>
<?php
$this->headLink()
          ->prependStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Ynauction/externals/styles/jquery.bxslider.css');
$this->headScript()
        ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/jquery-1.7.1.min.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/jquery.bxslider.min.js')
;
$datas = $this->datas;
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
?>
<?php if(Count($datas) <= 0): ?>              
       <div class="tip" style="clear: inherit;">
              <span>
                   <?php echo $this->translate('There are no featured auctions yet.');?>
              </span>
           <div style="clear: both;"></div>
       </div>
<?php else:  ?>
<ul class="" id ="slideshow_container">
<li style="position:relative; overflow: hidden;">
        <div id ="slide-runner-widget" class='slideshow'>
<?php foreach($datas as $data):
?>    <div class="slide">
       <div style="margin-bottom: 15px;"> 
            <div valign='top' style='text-align: center; padding-top:7px; padding-left: 2px;  padding-bottom:6px; float: left; position:relative; overflow: hidden;'>
                 <?php if($data->getPhotoUrl() != ""): ?>  
                   <a href="<?php echo $data->getHref()?>" title="<?php echo $data->title?>"><span style="background-image: url('<?php echo $data->getPhotoUrl("thumb.profile")?>'); width:155px; height:155px; background-repeat: no-repeat; display:block"></span></a>
                 <?php else: ?>
                     <span title="<?php echo $data->title?>" style="background-image: url('./application/modules/Ynauction/externals/images/nophoto_product_thumb_profile.png'); width:155px; height:155px; background-repeat: no-repeat; display:block"></span>
                <?php endif; ?>
            <div style="position: absolute; left: 0pt; top: 5px; background: url('./application/modules/Ynauction/externals/images/auction_ft_bg.png') no-repeat scroll left top transparent; color: red; width: 52px; height: 46px; text-transform: uppercase; font-size: 9px;">
            </div> 
            </div>   
            <div valign='top' class="contentbox" style="width: 63%; padding-left: 20px; float: left;" >
                <span id="title" style="font-size: 14pt; font-weight: bold;">
                    <?php echo $this->htmlLink($data->getHref(), $data->getTitle()) ?>
                </span>
                <div class="ynauctions_browse_date">              
				    <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($data->getOwner()->getHref(), $data->getOwner()->getTitle()) ?>
                    <?php echo $this->timestamp(strtotime($data->creation_date))?> 
                </div>
                <div class="ynauctions_browse_date">
                     <?php echo $this->translate('Bid history');?>:  
                     <a href="<?php echo $data->getHref() ?>"><span id="total"> <?php echo $data->total_bids; ?></span> 
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
                         <?php if($data->status != 0):?>
                               <font color="silver" style="font-weight: bold;"><?php echo $this->translate('Ended'); ?></font>
                         <?php elseif($data->stop == 1 && $data->status == 0):?>
                               <font color="silver" style="font-weight: bold;"><?php echo $this->translate('Stopped'); ?></font>
                         <?php else:?>
                         <span style='color:#666666'> <?php echo $data->getDealEndIn(); ?> </span>
                         <?php endif;?>
                         </span>
                     </div>
                 </div>
                 
                 <div class="ynprice">
                    <div class="group_price" style="width: 100px;">
                        <span class="label"><?php echo $this->translate('Starting Price');?></span>  <br/>
                        <span class="price"> <?php echo $this->locale()->toCurrency($data->starting_bidprice,$data->currency_symbol); ?></span>
                    </div>
                    <div class="group_price" style="width: 100px;">
                        <span class="label"><?php echo $this->translate('BuyOut Price');?></span>  <br/>
                        <span class="price"> <?php 
                        if($data->price != 0)
                            echo $this->locale()->toCurrency($data->price,$data->currency_symbol); 
                        else
                            echo $this->translate("N/A");?></span>
                    </div>
                    <div class="group_price" style="width: 100px;">
                        <span class="label"><?php echo $this->translate('Current Price');?></span>  <br/>
                        <span class="price"> 
                              <?php
                              echo $this->locale()->toCurrency($data->bid_price,$data->currency_symbol);
                               ?>
                        </span>
                    </div>
                 </div>
            </div>
       </div>
   </div>   
 <?php endforeach;?>
     </div>
    </li>
 </ul>  
 <script type="text/javascript">
    jQuery.noConflict();
    
    en4.core.runonce.add(function(){
        jQuery('#slide-runner-widget').bxSlider({
            auto: true,
            touchEnabled:false,
            controls: false
        });
    });
</script>
 <?php endif; ?>
