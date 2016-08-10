<?php
/**
 * SocialEngine
 *
 * @category   Application_Themes
 * @package    Template
 * @copyright  Copyright YouNet Company
 */
?>

<?php if($this->col_style == 1): ?>
  <div class="row">
    <?php foreach ($this->items as $item): ?>
    <div class="<?php echo $this->col_class;?>">
      <div class="ybo_eachBlogs clearfix">
        <?php 
        $photo_item = $item; 
		if($item -> getType() == "blog")
		{
			$photo_item = $item->getOwner();
		}
        echo $this->htmlLink($item->getHref(), $this->itemPhoto($photo_item, 'thumb.icon'), array('class' => 'linkthumb')) ?>
        <div class="desc">
          <?php if($this->show_title): ?>
          <h4>
            <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->title, 20), array('title' => $item->title));?>
          </h4>
          <?php endif; ?>
          <?php if($this->show_description): ?>
          <p><?php echo $this->string()->truncate($this->string()->stripTags($item->getDescription()), 290) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


<?php if($this->col_style == 2): ?>
  <div class="row">
    <?php foreach ($this->items as $item): ?>
    <div class="<?php echo $this->col_class;?>">
      <div class="thumbnail-style thumbnail-kenburn">
        <div class="thumbnail-img">
          <div class="overflow-hidden"><?php 
          	$photo_item = $item; 
			if($item -> getType == "blog")
			{
				$photo_item = $item->getOwner();
			}?>
          	<a href="<?php echo $item->getHref(); ?>" class="bg-thumb-effect" style="background-image: url('<?php echo $photo_item->getPhotoUrl(); ?>')"></a>
          </div>
          <?php if($this->show_readmore): ?>          
	          <?php echo $this->htmlLink($item->getHref(), $this->translate('read more +'), array(
	            'class' => 'btn-more hover-effect',
	          )) ?>           
          <?php endif; ?>
        </div>
        <?php if($this->show_title): ?>
        <h3>
          <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20), array('title' => $item->getTitle(), 'class' => 'hover-effect'));?>
        </h3>
        <?php endif; ?>
        <?php if($this->show_description): ?>
        <p><?php echo $this->string()->truncate($this->string()->stripTags($item->getDescription()), 200) ?></p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>