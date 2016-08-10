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
  <?php
  function selfURL() {
     $server_array = explode("/", $_SERVER['PHP_SELF']);
      $server_array_mod = array_pop($server_array);
      if($server_array[count($server_array)-1] == "admin") { $server_array_mod = array_pop($server_array); }
      $server_info = implode("/", $server_array);
      return "http://".$_SERVER['HTTP_HOST'].$server_info."/";
 } 
 ?>
 <script type="text/javascript"> 
var fr  = null;
var is_already = true;
 function makeBill(f,block_id)
{
    if(f == null || f == undefined && is_already == false){     
      fr.submit();
       
    }else{
         fr =  f;
         is_already = false;
         new Request.JSON({
          url: '<?php echo $this->url(array("module"=>"ynauction","controller"=>"account","action"=>"makebill"), "default") ?>',
          data: {
            'format': 'json',
            'block' : block_id
          },
          'onComplete':function(responseObject)
            {  
                makeBill();
            }
        }).send();
        return false; 
    }   
    return false;
}
</script>
<?php 
 //$user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
 //$info_account = Ynauction_Api_Account::getCurrentAccount($user_id);
//if ($info_account['account_username'] == ''): ?>
<!--<div class="tip">
      <span>
             <?php echo $this->translate('You do not have any finance account yet. '); ?><a href="<?php echo selfURL(); ?>auction/account/create"><?php echo $this->translate('Click here'); ?></a> <?php echo $this->translate('  to add your account.'); ?>
        </span>
    </div>      
              <?php// else: ?>
              -->
 <div class='clear'>
  <div style="padding-left: 2px;">
      <h3><?php echo $this->translate("Blocks Of Bids") ?></h3>  
    <?php if(count($this->blocks)>0):?>

      <table  width="70%">
        <thead>
          <tr style="background:#2C2C2C none repeat scroll 0 0;">
            <th width="40%" style="padding-top: 5px;padding-bottom: 5px;color:#FFF; padding-left: 2px;"><?php echo $this->translate("Block Name") ?></th>
            <th width="20%"  style="padding-top: 5px;padding-bottom: 5px;color:#FFF;"><?php echo $this->translate("Price (".Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD').")") ?></th>
            <th width="20%"  style="padding-top: 5px;padding-bottom: 5px;color:#FFF;"><?php echo $this->translate("Bids") ?></th>
            <th width="20%"  style="padding-top: 5px;padding-bottom: 5px;color:#FFF;"><?php echo $this->translate("Pay with paypal") ?></th>
          </tr>

        </thead>
        <tbody>
          <?php foreach ($this->blocks as $block): ?>
          <tr>
            <td class="account"><?php echo $block->title?></td>
            <td class="account" style="font-weight: bold;"><?php echo number_format($block->price,2)?></td>
            <td class="account" style="font-weight: bold;"><?php echo number_format($block->bids)?></td>
            <td class="account">
            <?php if($block->price == 0):
             if($block->checkbuyblock()):   ?>
                        <form action="<?php echo selfURL() ?>auction/account/buyblock0/block/<?php echo $block->block_id  ?>/bids/<?php echo $block->bids ?>" method="POST" name="cart_form"> 
                         <button  name="buybid0" type="submit" ><?php echo $this->translate('Buy now');?></button>   
                         </form>
             <?php
                    else:
                        echo $this->translate('You bought');
                    endif;
            else: ?>              
                          <form action="<?php echo $this->paymentForm;?>" method="post" name="cart_form" id = "<?php echo $block->block_id?>" onsubmit="return makeBill(this,<?php echo $block->block_id?>);">
                        <div class="p_4">
                           <button  name="minh" type="submit" style="float: left; " ><?php echo $this->translate('Buy now');?></button>
                           <input TYPE="hidden" NAME="cmd" VALUE="_xclick"/>
                           <input TYPE="hidden" NAME="business" VALUE="<?php echo $this->receiver['email']?>"/>
                           <input TYPE="hidden" NAME="amount" VALUE="<?php echo $block->price;?>"/>
                           <input TYPE="hidden" NAME="currency_code" VALUE="<?php echo $this->currency;?>"/>
                           <input TYPE="hidden" NAME="description" VALUE="Pay auction"/>
                           <input type="hidden" name="notify_url" value="<?php echo $this->paramPay['ipnNotificationUrl']?>"/>
                           <input type="hidden" name="return" value="<?php echo $this->paramPay['returnUrl']?>"/>
                           <input type="hidden" name="cancel_return" value="<?php echo $this->paramPay['cancelUrl']?>"/>
                           <input type="hidden" name="no_shipping" value="1"/>
                           <input type="hidden" name="no_note" value="1"/>
                           <div>
                           
                        </div>
                    </form>
             <?php endif; ?>       
            </td>
          </tr>

          <?php endforeach; ?>

        </tbody>
      </table>

      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no blocks.") ?></span>
      </div>
      <?php endif;?>
  </div>
</div>
<?php//  endif;?>
