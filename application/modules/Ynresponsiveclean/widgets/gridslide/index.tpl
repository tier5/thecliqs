<?php
/**
 * SocialEngine
 *
 * @category   Application_Themes
 * @package    Template
 * @copyright  Copyright YouNet Company
 */
?>

<div class="row overflow-hidden slider-grid-main">
  <div class="widget-photo-slide-grid clearfix" id="<?php echo $this->id_gridslide; ?>">
    <?php if($this->col_style == 1): ?>  
        <?php foreach ($this->items as $item): 
			$user = $item -> getOwner();
			?>
        <div class="slide-element <?php echo $this->col_class;?>">
          <div class="ybo_eachBlogs clearfix">
            <div class="overflow-hidden">
              <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.main')) ?>
              <a href="<?php echo $item->getHref(); ?>" class="photoSpan" style="background-image: url(<?php echo $item->getPhotoUrl(); ?>)"></a>
            </div>
            <div class="desc">              
              <?php if($this->show_title): ?>
              <h4>
                <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->title, 20), array('title' => $item->title));?>
              </h4>
              <?php endif; ?>
              <?php if($item -> getType() == "event"):?>
	              <div class="username"><?php echo $this -> translate('Led by %s', $user);?></div>
	              <?php 
			          $start_date = strtotime($item->starttime);
			          $oldTz = date_default_timezone_get();
			          if($this->viewer() && $this->viewer()->getIdentity())
			          {
			             date_default_timezone_set($this -> viewer() -> timezone);
			          }
			          else 
			          {
			             date_default_timezone_set( $this->locale() -> getTimezone());
			          }
			          $startTime = date("M d, Y", $start_date); 
			          date_default_timezone_set($oldTz);
			       ?>
	              <div class="start"><i class="fa fa-clock-o"></i>&nbsp;<?php echo $startTime; ?></div>
	              <?php if($item->location):?>
	              	<div class="location"><i class="fa fa-map-marker"></i>&nbsp;<?php echo $item->location; ?></div>
              	  <?php endif; ?>
	          <?php else: ?>
	          	 <div class="username"><?php echo $this -> translate('by %s', $user); ?></div>
              	 <div><?php echo $this -> translate(array("%s view", "%s views", $item -> view_count), $item -> view_count) ?> - <?php echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count); ?></div>
	          <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if($this->col_style == 2): ?>
          <?php foreach ($this->items as $item): 
                $user = $item -> getOwner();
                ?>
          <div class="slide-element <?php echo $this->col_class;?>">
            <div class="thumbnail-style thumbnail-kenburn">
              <div class="thumbnail-img">
                <div class="overflow-hidden">
                    <?php 
                       $photoUrl = $item ->getPhotoUrl();
                       if (!$photoUrl && $item -> getType() == "blog" && Engine_Api::_()->hasModuleBootstrap('ynblog'))  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_blog_thumb_main.png';
                    ?>
                    <a href="<?php echo $item->getHref(); ?>" class="bg-thumb-effect" style="background-image: url('<?php echo $photoUrl ?>')">
                   </a>
                </div>           
              </div>
              <?php if($this->show_title): ?>
              <h3>
                <?php echo $this->htmlLink($item->getHref(),$this->string()->truncate($item->getTitle(), 20), array('title' => $item->getTitle(), 'class' => 'hover-effect'));?>
              </h3>
              <?php endif; ?>
              <div><?php echo $this -> translate('by %s', $user); ?></div>
              <div><?php echo $this -> translate(array("%s view", "%s views", $item -> view_count), $item -> view_count) ?> - <?php if($item -> getType() == "event") echo $this -> translate(array("%s guest", "%s guests", $item -> member_count), $item -> member_count); else echo $this -> translate(array("%s comment", "%s comments", $item -> comment_count), $item -> comment_count); ?></div>
            </div>
          </div>
          <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
