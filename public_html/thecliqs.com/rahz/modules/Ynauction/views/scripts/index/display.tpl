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
 $item = $this->product;      
  ?> 
<script type="text/javascript"> 
var fr  = null;
var is_already = true;
 function makeBill(f)
{
    if(f == null || f == undefined && is_already == false){     
      fr.submit();
       
    }else{
         fr =  f;
         is_already = false;
         new Request.JSON({
          url: '<?php echo $this->url(array("module"=>"ynauction","controller"=>"index","action"=>"makebill"), "default") ?>',
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
    <div class="yntable noborder ynaution_display_index clearfix">
        <div class="ynaution_display_index_thumbs">
            <a href="<?php echo $item->getHref()?>" title="<?php echo $item->title?>">
                <img src="<?php if($item->getPhotoUrl("thumb.profile") != ""): echo $item->getPhotoUrl("thumb.profile"); else: echo 'application/modules/Ynauction/externals/images/nophoto_product_thumb_profile.png'; endif;?>" style = "max-width:250px;max-height:250px" />
            </a>
        </div>
        <div class="ynaution_display_index_detail">
             <strong id="title"><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?> </strong>
                 <div id="body" style="padding-top:5px;" class="ynauction_list_description">
                  <?php echo substr(strip_tags($item->description), 0, 350); if (strlen($item->description)>350) echo "..."; ?>
                  </div>
                  <?php 
                  $user = Engine_Api::_()->user()->getViewer();
                  $freeF = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $user, 'free_fee');
                   $freeP = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $user, 'free_display');
                   if($freeP == 1 && $freeF == 1):
                      $item->total_fee = 0;  
                   endif;
                  ?> 
                 <?php if($item->total_fee > 0): ?>
               <div style="padding-top:5px;" >
               <span style="font-weight: bold;" class="ynaution_span"> <?php echo $this->translate('Total fee: ');?></span> 
               <font color="red" style="font-weight: bold;"><?php echo number_format($item->total_fee,2);?> <?php echo $this->currency ?></font>
               </div>
               <?php endif; ?>
                <br/>
                <div class="clearfix">
                <?php if($item->total_fee > 0): ?>
	                <form method="post" action="<?php echo $this->escape($this->url(array('action' => 'update-order'), 'ynauction_general', true)) ?>"
				        class="global_form" enctype="application/x-www-form-urlencoded">
				    <div>
				      <div>
				        <div class="form-elements">
				          <div id="buttons-wrapper" class="form-wrapper">
				            <?php foreach( $this->gateways as $gatewayInfo ):
				              $gateway = $gatewayInfo['gateway'];
				              $plugin = $gatewayInfo['plugin'];
				              $first = ( !isset($first) ? true : false );
				              ?>
				              <button style="margin-top: 5px" type="submit" name="gateway_id" value="<?php echo $gateway->gateway_id ?>">
				                <?php echo $this->translate('Pay with')." ".$this->translate($gateway->title) ?>
				              </button>
				               	 <?php echo $this->translate(' or ') ?>
				            <?php endforeach; ?>
				            <input type="hidden" name="id" value="<?php echo $item -> getIdentity()?>"/>
							   <a href="<?php echo $this->url(array(),'ynauction_general',true); ?>"> <?php echo $this->translate('cancel') ?> </a>
				          </div>
				        </div>
				      </div>
				    </div>
				  </form>
            <?php else: ?>
            <form action="<?php echo selfURL() ?>auction/publish/auction/<?php echo $item->product_id  ?>" method="POST" name="cart_form">
            <div class="p_4">
                   <button  name="publish" type="submit" style="float: left; " ><?php echo $this->translate('Publish');?></button>
                    <div style="float: left; margin-top: 7px; padding-left: 10px;">
                    <?php echo $this->translate('Or ') ?>  
                   <?php echo $this->htmlLink(array(
                      'action' => 'manageauction',
                        'route' => 'ynauction_general',
                    ), $this->translate('Cancel'), array(
                      'style' => 'font-weight: bold;',
                    )) ?>
                    </div>  
                </div>
            </form>
            <?php endif; ?>
            </div>
        </div>
    </div>    
 </div>
