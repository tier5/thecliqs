<?php
if($this->html_ynresponsive_slideshow)
{
	echo $this->html_ynresponsive_slideshow;
}
else
{
?>
	<ul class="yncontest_$item_container" id ="yncontest_slideshow_container">
		<li class="ynContest_liInfo" style="height:<?php echo $this->height + 55;?>px">
			<div id="push" class="slideshow"></div>
		</li>
	</ul>
	<script type = "text/javascript">
		window.addEvent('domready', function(){
			var data = { 
				<?php foreach($this->items as $item): ?>
				'<?php echo $item->getPhotoUrl();?>': { caption: 
					'<div class="icon <?php echo $item->contest_type?>" style="top:-<?php echo $this->height - 52 ?>px"></div><div class="yncontest_title" style="top:-<?php echo $this->height - 62 ?>px"><a href="<?php echo $item->getHref()?>"><?php echo htmlspecialchars($item->getTitle(), ENT_QUOTES)?></a></div><div class="description"><div class="column first"><p><?php echo $this->translate('Created by').": "; echo $item->getOwner();?></p><p><?php echo $this->translate('End Contest On').": ";?></span><span><?php echo  $this->locale()->toDate( $item->end_date, array('size' => 'long')) ;?></p></div><div class="column center"><p><?php echo $this->translate('Entries');?></p><strong><?php echo $item->entries;?></strong></div><div class="column center"><p><?php echo $this->translate('Participants');?></p><strong><?php echo $item->participants;?></strong></div><div class="column center"><p><?php echo $this->translate('Submit Entries');?></p><strong class="orange"><?php 
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
						
						?></strong></div></div>' },
				<?php endforeach;?>					
			};
			
			var widths = $$('.layout_yncontest_featured_contest')[0].getCoordinates().width;
			switch('<?php echo $this->slider_action;?>')
			{
				case 'overlap':
					new Slideshow('push', data, { captions: { delay: 1000 }, delay: 3000, height: <?php echo $this->height?>, hu: '', width: widths });
					break;
				case 'noOverlap':
					new Slideshow('push', data, { height: <?php echo $this->height?>, hu: '', overlap: false, resize: 'fit', width: widths});
					break;
				case 'flash':
					new Slideshow.Flash('push', data, { color: ['tomato', 'palegreen', 'orangered', 'aquamarine'], height: <?php echo $this->height?>, hu: '', width: widths });
					break;
				case 'fold':
					new Slideshow.Fold('push', data, { height: <?php echo $this->height?>, hu: '', width: widths });
					break;
				case 'kenburns':
					new Slideshow.KenBurns('push', data, { duration: 1500, height: <?php echo $this->height?>, hu: '', width: widths });
					break;
				default:
					new Slideshow.Push('push', data, { height: <?php echo $this->height?>, hu: '', transition: 'back:in:out', width: widths });
					break;
			}
		});
	</script>
<?php
}
?>