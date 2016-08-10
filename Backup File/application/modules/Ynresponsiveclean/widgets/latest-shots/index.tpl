<?php
/**
 * SocialEngine
 *
 * @category   Application_Themes
 * @package    Template
 * @copyright  Copyright YouNet Company
 */
?>

<?php $index = 0; ?>
<!-- Latest Shots -->
<div class="col-md-4 margin-bottom-20">
	<div class="ybo_headline">
		<h3><?php echo $this->translate("Latest Shots") ?></h3>
	</div>
	<div id="myCarousel" class="carousel slide">
		<div class="carousel-inner">
			<?php foreach( $this->top_photos as $itemTopPhoto): ?>		
			<div class="item <?php echo ++ $index == 1?'active':'';?>">
				<?php echo $this->htmlImage($itemTopPhoto->getPhotoUrl(), $itemTopPhoto->getTitle(), array('id' => 'media_photo')); ?>
				<div class="carousel-caption">
					<p><?php echo $this->htmlLink($itemTopPhoto->getHref(), $itemTopPhoto->getTitle()) ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>		
		<div class="carousel-arrow">
			<a class="left carousel-control" href="#myCarousel" data-slide="prev"><i class="icon-angle-left"></i></a>
			<a class="right carousel-control" href="#myCarousel" data-slide="next"><i class="icon-angle-right"></i></a>
		</div>
	</div>
</div><!--/col-md-4-->