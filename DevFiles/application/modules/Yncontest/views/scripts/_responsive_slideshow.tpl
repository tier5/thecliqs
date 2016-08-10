<div class="yncontest_responsive_slideshow_flex flexslider" id="flexslider_<?php echo $this->slider_id; ?>">
  <ul class="slides">
    <?php foreach( $this->items as $item): ?>    
    <?php $title  =  $item->getTitle(); ?>
    <li class="<?php echo ++$index==1?'active':''; ?>">
      <div class="overflow-hidden" style="height:350px">
		  <span style="background-image: url(<?php echo $item -> getPhotoUrl()?>);"></span>
            <div class="carousel-caption slideshow-captions">
              <p><?php echo $this->htmlLink($item->getHref(), $title) ?></p>
              <div class="description">
              	<div class="column first">
	              <p>
	              		<?php echo $this->translate('Created by').": ";?>
	              		<?php echo $this -> htmlLink($item->getOwner() -> getHref(), $this -> string() -> truncate($item->getOwner() -> getTitle(), 20));?>
	              </p>
	              <p>
	              	<?php echo $this->translate('End Contest On').": ";?>
	              	<?php echo  $this->locale()->toDate( $item->end_date, array('size' => 'long')) ;?>
	              </p>
              	</div>
	              <div class="column center">
	              	<p><?php echo $this->translate('Entries');?></p>
	              	<strong><?php echo $item->entries;?></strong>
	              </div>
	              <div class="column center">
	              	<p><?php echo $this->translate('Participants');?></p>
	              	<strong><?php echo $item->participants;?></strong>
	              </div>
	              <div class="column center">
	              	<p><?php echo $this->translate('Submit Entries');?></p>
	              	<strong class="orange">
	              	<?php
	              		if($item->start_date_submit_entries > date('Y-m-d H:i:s'))
						{
							echo $this->translate("<i>(Opening)</i>");
						}
						elseif($item->end_date_submit_entries < date('Y-m-d H:i:s'))
						{	
							echo $this->translate("End");	
						}	
						else
						{
							if($item -> yearleft >= 1)
						   		echo $this->translate(array('%s year left','%s years left',$item -> yearleft),$item -> yearleft);
							elseif($item -> monthleft >= 1)
								echo $this->translate(array('%s month left','%s months left',$item -> monthleft),$item -> monthleft);
								
							elseif($item -> dayleft >= 1)
								echo $this->translate(array('%s day left','%s days left',$item -> dayleft),$item -> dayleft);							
							else {							
								echo  $this->translate(array('%s hour %s minute left','%s hours %s minutes left', $item -> hourleft, $item -> minuteleft), $item -> hourleft, $item -> minuteleft);														
							}
						}
						?>
					</strong>
	              </div>
            	</div>
            </div>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
<script type="text/javascript">
jQuery(window).load(function() {
  jQuery('#flexslider_<?php echo $this -> slider_id; ?>').flexslider({
    animation: "slide",
	auto: 3000,
    speed: 1000
  });
});
</script>