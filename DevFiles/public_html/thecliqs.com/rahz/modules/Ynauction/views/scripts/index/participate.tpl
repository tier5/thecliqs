 <?php  $this->locale()->setLocale("en_US"); ?>
<div class='layout_right'>
  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="quicklinks">
      <?php
        // Render the menu
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>
<div class='layout_middle'> 
<?php
$homeres = $this->paginator; 
$user_id = $this->viewer_id;
if(!function_exists("selfURL"))
 {
    function selfURL() {
         $server_array = explode("/", $_SERVER['PHP_SELF']);
          $server_array_mod = array_pop($server_array);
          if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
          $server_info = implode("/", $server_array);
          return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
     }  
 }    
 ?>
 <?php if(Count($homeres) <= 0): ?>              
       <div class="tip" style="clear: inherit;">
      <span>
           <?php echo $this->translate('There are no auctions.');?>
      </span>
           <div style="clear: both;"></div>
    </div>
<?php else: $now = date('Y-m-d H:i:s'); ?>
<ul>
<?php foreach($homeres as $data): 
if($data->status != 0 || $data->end_time <= $now || $data->status == 3): 
                         if($data->bider_id == 0)
                            $data->status = 3; 
                         else
                            $data->status = 1; 
                         $data->stop = 1;
                         $data->save(); endif;?>
          <li>
            <div valign='top' style='text-align: center; padding-top:6px;  padding-bottom:6px; float: left;'>
                 <?php if($data->getPhotoUrl() != ""): ?>  
                   <a href="<?php echo $data->getHref()?>" title="<?php echo $data->title?>"><img src="<?php echo $data->getPhotoUrl("thumb.normal")?>" style = "max-width:100px;max-height:100px" /></a>
                 <?php else: ?>
                    <img src="./application/modules/Ynauction/externals/images/nophoto_product_thumb_normal.png" title="<?php echo $data->title?>" style = "max-width:100px;max-height:100px" />                                                                                                             
                <?php endif; ?>   
            </div>   
            <div valign='top' class="contentbox" style="width: 75%; padding-left: 20px; float: left; padding-bottom: 10px;" >
                <span id="title" style="font-size: 11pt; font-weight: bold;">
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
                        <span class="price"> 
                              <?php
                              echo $this->locale()->toCurrency($data->bid_price,Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD'));
                               ?>
                        </span>
                    </div>
                 </div>
            </div>
 </li>
 <?php endforeach; ?>
 </ul>
 <?php  endif; ?>
 <br/>
<div>
  <?php echo $this->paginationControl($this->paginator, null, array("pagination/auctionpagination.tpl","ynauction"), array("orderby"=>$this->orderby)); ?>                                                                                                                                                                               
</div>
</div>

