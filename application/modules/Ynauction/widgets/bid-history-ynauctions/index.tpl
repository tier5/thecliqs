   <?php  $this->locale()->setLocale("en_US"); ?>
  <?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>
 <div id="bids_his" style="padding: 5px;">       
      <?php $lasts = Engine_Api::_()->ynauction()->getBidHis($this->product->product_id,20);
       $cViewMore = 0; ?>
 <?php if(Count($lasts ) <= 0): ?>
    <div style="padding-left: 10px; float: left; padding-right: 20px;">
    <?php echo $this->translate("Highest Bidder"); ?>:
    <?php 
    echo  $this->translate("Nobody");?>
     </div>
     <div style="padding-right: 20px;  float: left;">
    <?php echo $this->translate("Bidder"); ?>:
    <?php echo  "0";?>
     </div>
    <div style="float: left;">
    <?php echo $this->translate("Bids"); ?>:
    <?php echo "0" ?>
    </div>
    <div style="float: right; margin-top: -5px;;">
    <a style="background-image: url('./application/modules/Ynauction/externals/images/refresh.png'); background-repeat: no-repeat; padding-left: 26px; padding-bottom: 10px; font-size: 11pt" href="javascript:;" onclick="refresh_list(<?php echo $this->product->product_id; ?>)"><?php echo $this->translate("Refresh list") ?></a>
    </div>
     <br/> 
       <div class="tip">
      <span>
           <?php echo $this->translate('There are no bids yet.');?>
      </span>
           <div style="clear: both;"></div>
       </div>
   <?php else: ?>
    <div style="padding-left: 10px; float: left; padding-right: 20px;">
    <?php echo $this->translate("Highest Bidder"); ?>:
    <?php $bider = Engine_Api::_()->getItem('user', $this->product->bider_id);  
    echo  $bider?>
     </div>
     <div style="padding-right: 20px;  float: left;">
    <?php echo $this->translate("Bidder"); ?>:
    <?php echo  $this->product->getUserBids();?>
     </div>
    <div style="float: left;">
    <?php echo $this->translate("Bids"); ?>:
    <?php echo $this->product->total_bids ?>
    </div>
    <div style="float: right; margin-top: -5px;" class="bid_refresh">
    <a style="background-image: url('./application/modules/Ynauction/externals/images/refresh.png'); background-repeat: no-repeat; padding-left: 26px; padding-bottom: 10px; font-size: 11pt" href="javascript:;" onclick="refresh_list(<?php echo $this->product->product_id; ?>)"><?php echo $this->translate("Refresh list") ?></a>
    </div>
    <ul class="global_form_box" style="color: black; background: none; padding: 0px; border-color: #EBEFF0; border-width: 2px; margin-top: 20px;">
    <table cellpadding="0" cellspacing="0" width="100%">
    <tr style="background:#E5E5E5 none repeat scroll 0 0; height: 35px;">
        <td height="25px" width="30%" style="padding:2px 2px 2px 7px;"><?php echo $this->translate("Name");?></td>
        <td style="padding:2px;"><?php echo $this->translate('Bid Amount') ?></td> 
        <td style="padding:2px;"><?php echo $this->translate('Bid Time') ?></td>
    </tr>
   <?php foreach($lasts as $last):
    $bider = Engine_Api::_()->getItem('user', $last->ynauction_user_id);
    $product = Engine_Api::_()->getItem('ynauction_product', $last->product_id); ?>
    <?php
    if($cViewMore < 10){  $cViewMore += 1;  ?> 
     <tr style="border-bottom : 1px solid #F1F1F1; height: 35px;">
        <td width="30%" style="padding:7px;">           
            <?php echo $bider; ?>
        </td>
        <td>
            <?php echo $this->locale()->toCurrency($last->product_price,$product->currency_symbol); ?>
        </td>
        <td>
            <?php echo $this->locale()->toDateTime($last->bid_time);?>
        </td> 
    </tr>   
    <?php }
    else{?>
        <tr id="linkViewMore_<?php echo $cViewMore;?>" style="display: none; border-bottom : 1px solid #F1F1F1; height: 35px;">
        <td width="50%" style="padding:7px;">           
            <?php echo $bider; ?>
        </td>
        <td>
            <?php echo $this->locale()->toCurrency($last->product_price,$product->currency_symbol); ?>
        </td>
        <td>
            <?php echo $this->locale()->toDateTime($last->bid_time);?>
        </td>     
        </tr>
            <?php
            $cViewMore += 1;
            }
            ?>
   <?php endforeach;?>
   <?php if(Count($lasts ) > 10): ?>
   <tr id="linkViewMore" style="height: 40px;"> 
   <td style="padding-left: 8px;">
   <a href="javascript:void(0);" onclick="viewMoreTopBid();">
   &raquo; 
   <?php echo $this->translate('View More');?>
   </a>
   </td> 
   </tr> 
   <?php endif; ?>
   </table>
   </ul> 
   <?php endif;?>
    </div>
<script language="JavaScript">
        function viewMoreTopBid() {
            var cViewMoreA = <?php echo $cViewMore;?>;
            document.getElementById('linkViewMore').style.display = 'none';
            for(var i = 10; i < cViewMoreA; i++){
               document.getElementById('linkViewMore_'+i).style.display = '';
            }
            return true;
        }
</script>        