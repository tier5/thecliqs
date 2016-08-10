<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>
<?php
    $owner = $this->video->getOwner();
?>
<span class="video_views">
	<a href='<?php echo $this->video->getHref();?>'><?php echo substr($this->video->title, 0, 40);?></a>
	<br/>
    <?php if ($owner->getIdentity()) : ?>
        &nbsp;<?php echo $this->translate('By %s', $owner->__toString()) ?>
    <?php endif; ?>
    |&nbsp;    
    <?php echo $this->translate(array('%1$s view', '%1$s views', $this->video->view_count), $this->locale()->toNumber($this->video->view_count)) ?>
</span>