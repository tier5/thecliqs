
  <?php
	 $this->locale()->setLocale("en_US");
  ?>
  <img src='./application/modules/Ynauction/externals/images/auction-myaccount.png' width="48px" height="48px" border='0' class='icon_big' style="margin-bottom: 15px;">
  <div class='page_header'><?php echo $this->translate('My Account'); ?></div>
  <div style="overflow: hidden">
    <?php echo $this->translate('Personal Finance Account Management.'); ?><span><a href="<?php echo $this->url(array('action' => 'transaction'),'ynauction_general'); ?>"> <?php echo $this->translate('View my transaction history'); ?></a></span><br />
  </div>


    <?php $info_user = $this->info_user; ?>
    <div class="ynauction-space-line"></div>

    <div style="margin-bottom: 10px;">
        <div class="yntable noborder clearfix ynaction_myaccount">
            <div class="yntable-item">
                <h3 style="margin-bottom: 10px;">
                    <?php echo $this->translate('User Information'); ?>
                </h3>
                <table cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr class="ynauction_account">
                        <td width="35%" >
                            <?php echo $this->translate('Username'); ?>:
                        </td>
                        <td>
                             <?php echo $info_user->username?>
                        </td>
                    </tr>
                    <tr class="ynauction_account">
                        <td>
                        <?php echo $this->translate('Full name'); ?>:
                        </td>
                        <td>
                        <?php echo $info_user->displayname?>
                        </td>
                    </tr>
                    <tr class="ynauction_account">
                        <td>
                            <?php echo $this->translate('Email'); ?>:
                        </td>
                        <td>
                        	<div class='eclipse_text'><?php echo $info_user->email ?></div> 
                        </td>
                    </tr>
                     <tr class="ynauction_account">
                        <td>
                            <?php echo $this->translate('Paypal account'); ?>:
                        </td>
                        <td>                    	
                            <?php if($this->info_account['account_username']): ?>
                             	<div class='eclipse_text'><?php echo $this->info_account["account_username"];?></div>  
                            <?php else: ?>
                            <div class="message" style="float: left;"><?php echo $this->translate('You do not have any paypal account yet. '); ?><a href="<?php echo selfURL(); ?>auction/account/create"><?php echo $this->translate(' Click here'); ?></a> <?php echo $this->translate('  to add paypal account.'); ?></div>
                            <?php endif; ?>
                            
                        </td>
                    </tr>
                    <tr >
                        <td align="right" style="height: 30px; padding-top: 10px;">
                            <div class="p_4" style="float: left;">
                             <form method="post" action="<?php echo $this->url(array('action'=>'edit'),'ynauction_account'); ?>">
                                <button type="submit" name="editperionalinfo"><?php echo $this->translate('Edit Information'); ?> </button>
                             </form>
                            </div>
                        </td>
                    </tr>                
                </table>
            </div>
            <div class="yntable-item">
                <h3  style="margin-bottom: 10px;">
                    <?php echo $this->translate('Sold Items Summary'); ?>
                </h3>
                <table cellpadding="0" cellspacing="0" width="100%">
                    <tr style="background:#E9F4FA none repeat scroll 0 0;">
                         <td height="25px" width="60%" style="font-weight:bold;padding:2px 2px 2px 7px; "><?php echo $this->translate('Name'); ?></td>
                         <td style="font-weight:bold;padding:2px;text-align:center"><?php echo $this->translate('Price'); ?></td>
                    </tr>
                     <?php   $index = 0;
                        foreach($this->HistorySeller as $iProduct):  $index++;  
                        $product = Engine_Api::_()->getItem('ynauction_product', $iProduct->product_id); ?>
                             <tr>
                                <td width="70%" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                                 <strong><a class="eclipse_text" href="<?php echo $product->getHref(); ?>"> <?php echo $iProduct->title;?> </a></strong>
                                </td>    
                                <td width="15%" style="padding:7px;border-bottom:1px solid #E9F4FA;">
                                      <?php echo $this->locale()->toCurrency($iProduct->amount,$iProduct->currency_symbol); ?>
                                </td>
                                  
                            </tr>
                        <?php endforeach; ?>                          
                    <tr>
                    <?php if(count($this->HistorySeller)):?>
                    <td style=" padding-left: 5px; text-align: right" colspan="3" align="right">
                         <?php echo  $this->paginationControl($this->HistorySeller,null, null, null); ?>
                    </td>
                   <?php endif; ?>

                 </tr>
                </table>
            </div>
        </div>
      </div>
