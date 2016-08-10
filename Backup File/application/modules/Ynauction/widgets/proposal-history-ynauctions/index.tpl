   <?php  $this->locale()->setLocale("en_US"); ?>
  <?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>
 <div id="bids_his" style="padding: 5px;">       
      <?php $lasts = $this->proposals;
       $cViewMore = 0; ?>
    <div style="padding-left: 10px; float: left; padding-right: 50px;">
    <?php echo $this->translate("Seller assumes all responsibility for this listing"); ?>
     </div>
     <br/>
    <ul class="global_form_box" style="color: black; background: none; padding: 0px; border-color: #EBEFF0; border-width: 2px; margin-top: 20px;">
    <table cellpadding="0" cellspacing="0" width="100%">
    <tr style="background:#E5E5E5 none repeat scroll 0 0; height: 35px;">
        <td height="25px" width="30%" style="padding:2px 2px 2px 7px;"><?php echo $this->translate("Name");?></td>
        <td style="padding:2px;"><?php echo $this->translate('Proposal Amount') ?></td> 
        <td style="padding:2px;"><?php echo $this->translate('Proposal Time') ?></td>
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
            <?php echo $this->locale()->toCurrency($last->proposal_price,$product->currency_symbol); ?>
        </td>
        <td>
            <?php echo $this->locale()->toDateTime($last->proposal_time);?>
        </td> 
    </tr>   
    <?php }
    else{?>
        <tr id="linkViewMore_<?php echo $cViewMore;?>" style="display: none; border-bottom : 1px solid #F1F1F1; height: 35px;">
        <td width="50%" style="padding:7px;">           
            <?php echo $bider; ?>
        </td>
        <td>
            <?php echo $this->locale()->toCurrency($last->proposal_price,$product->currency_symbol); ?>
        </td>
        <td>
            <?php echo $this->locale()->toDateTime($last->proposal_time);?>
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