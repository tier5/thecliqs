<ul class="ynauctions_browse" style="padding: 5px;;">
   <h3><?php echo $this->translate("Introduction"); ?></h3> 
   <?php echo $this->product->description; ?>
   <br/>
   <h3><?php echo $this->translate("Description"); ?></h3>
   <?php echo $this->product->description1;?>
       <br/> 
   <a name="rateauction"></a>
          <div <?php if ($this->can_rate): ?>  onmouseout="rating_mouseout()" <?php endif;?>  id="auction_rate">
            <?php for($i = 1; $i <= 5; $i++): ?>
              <img width="20" id="rate_<?php print $i;?>"  <?php if ($this->can_rate): ?> style="cursor: pointer;" onclick="rate(<?php echo $i; ?>);" onmouseover="rating_mousehover(<?php echo $i; ?>);"<?php endif; ?> src="application/modules/Ynauction/externals/images/<?php if ($i <= $this->product->rates): ?>star_full.png<?php elseif( $i > $this->product->rates &&  ($i-1) <  $this->product->rates): ?>star_part.png<?php else: ?>star_none.png<?php endif; ?>" />
            <?php endfor; ?>
          </div>
  <?php echo $this->action("list", "comment", "core", array("type"=>"ynauction_product", "id"=>$this->product->getIdentity())) ?>

</ul>
<script type="text/javascript">
    var img_star_full = "application/modules/Ynauction/externals/images/star_full.png";
    var img_star_partial = "application/modules/Ynauction/externals/images/star_part.png";
    var img_star_none = "application/modules/Ynauction/externals/images/star_none.png";  
    
    function rating_mousehover(rating) {
        for(var x=1; x<=5; x++) {
          if(x <= rating) {
            $('rate_'+x).src = img_star_full;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }

    function rating_mouseout() {
        for(var x=1; x<=5; x++) {
          if(x <= <?php echo $this->product->rates ?>) {
            $('rate_'+x).src = img_star_full;
          } else if(<?php echo $this->product->rates ?> > (x-1) && x > <?php echo $this->product->rates ?>) {
            $('rate_'+x).src = img_star_partial;
          } else {
            $('rate_'+x).src = img_star_none;
          }
        }
    }
    function rate(rates){
        $('auction_rate').onmouseout = null;
        window.location = en4.core.baseUrl + 'auction/rate/auction_id/<?php echo $this->product->getIdentity();?>/rates/'+rates;
      }
  
</script>

 
