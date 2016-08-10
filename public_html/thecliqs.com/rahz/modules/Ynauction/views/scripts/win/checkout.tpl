 <?php  $this->locale()->setLocale("en_US"); ?>
<?php $this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Ynauction/externals/scripts/auction.js');   
       ?>
<div class="headline">
  <h2>
    <?php echo $this->translate('Auction');?>
  </h2>
  <div class="tabs">
    <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
    ?>
  </div>
</div>
<?php if($this->notView):  ?>
  <div class="tip">
      <span>
        <?php echo $this->translate('You can not view this page.');?>
      </span>
    </div>
  <?php
  else:
  function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 } 
 $item = $this->product;      
  ?> 
<script type="text/javascript"> 
var fr  = null;
var is_already = true;
 function makeBill(f)
{
    var check = document.getElementById('check'); 
    if(!check.checked)
    {
        alert('<?php echo $this->translate("Please read & agree to the Term of Service!");?>');
        return false;
    }
    if(f == null || f == undefined && is_already == false){     
      fr.submit();
       
    }else{
         fr =  f;
         is_already = false;
         new Request.JSON({
          url: '<?php echo $this->url(array("module"=>"ynauction","controller"=>"win","action"=>"makebill"), "default") ?>',
          data: {
            'format': 'json',
            'auction' : <?php echo $item->product_id; ?>
          },
          'onComplete':function(responseObject)
            {  
                makeBill();
            }
        }).send();
        return false; 
    }   
    return true;
}
 </script>
  <h2>
    <?php echo $this->translate('Product information');?>
  </h2>
<div class="table">    
    <table width="100%">
          <tr>
              <td valign='top' width='1' style=' text-align: center; padding-top:6px;  padding-bottom:6px; text-align: center;'>
               <a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>"><img src="<?php if($item->getPhotoUrl("thumb.profile") != ""): echo $item->getPhotoUrl("thumb.profile"); else: echo 'application/modules/Ynauction/externals/images/nophoto_product_thumb_profile.png'; endif;?>" style = "max-width:250px;max-height:250px" /></a>
            </td>
          <td valign='top' class="contentbox" style="width: auto; padding-left: 30px;">
          <strong id="title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </strong>
             <div id="body" style="padding-top:5px;" class="ynauction_list_description">
              <?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>350) echo "..."; ?>
              </div> 
           <div style="padding-top:5px;" >
           <span style="font-weight: bold;"> <?php echo $this->translate('Price: ');?></span> <font color="red" style="font-weight: bold;">
           <?php echo $this->locale()->toCurrency($item->bid_price,$item->currency_symbol);?></font>
           </div>
           
           <div style="padding-top:5px;">
            <span style="font-weight: bold;"><?php echo $this->translate('Seller: ');?></span><?php echo $item->getOwner(); ?> </div>
            <br/>
            <div>
            <input type="checkbox" id="check" value="0" name="check"/> <?php echo $this->translate('I have read & agreed to the') ?> 
            <a href = 'javascript:goto();' onclick="return goto()"><?php echo $this->translate('Term of Service'); ?></a>
            </div>
            <br/>
            <form action="<?php echo $this->paymentForm;?>" method="post" name="cart_form" onsubmit="return makeBill(this);">
            <div class="p_4">
               <button  name="minh" type="submit" style="float: left;" ><?php echo $this->translate('Pay with Paypal');?></button>
               <input TYPE="hidden" NAME="cmd" VALUE="_xclick"/>
               <input TYPE="hidden" NAME="business" VALUE="<?php echo $this->receiver['email']?>"/>
               <input TYPE="hidden" NAME="amount" VALUE="<?php echo $item->bid_price;?>"/>
               <input TYPE="hidden" NAME="currency_code" VALUE="<?php echo $item->currency_symbol;?>"/>
               <input TYPE="hidden" NAME="description" VALUE="Pay auction"/>
               <input type="hidden" name="notify_url" value="<?php echo $this->paramPay['ipnNotificationUrl']?>"/>
               <input type="hidden" name="return" value="<?php echo $this->paramPay['returnUrl']?>"/>
               <input type="hidden" name="cancel_return" value="<?php echo $this->paramPay['cancelUrl']?>"/>
               <input type="hidden" name="no_shipping" value="1"/>
               <input type="hidden" name="no_note" value="1"/>
                <div style="float: left; margin-top: 7px; padding-left: 10px;">
                <?php echo $this->translate('Or ') ?>  
               <?php echo $this->htmlLink(array(
                  'action' => 'winning',
                    'route' => 'ynauction_general',
                ), $this->translate('Cancel'), array(
                  'style' => 'font-weight: bold;',
                )) ?>
                </div>  
               <div>
            </div>
        </form>
        </td>           
    </tr>
    </table>
 </div>
 <?php endif; ?>
