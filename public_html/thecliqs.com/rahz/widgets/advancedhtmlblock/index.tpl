<style>
.layout_advancedhtmlblock ul{
	list-style-type: disc;
}	
.layout_advancedhtmlblock ul li{
	margin-left:20px;
}
</style>

<?php if (isset($this->title_data) && $this->title_data): ?>
<h3><?php echo $this->title_data ?></h3>
<?php endif; ?>



<?php if($this->isTablet && !$this->apply):?>
	<?php echo $this->tablet_data ?>

<?php elseif($this->isMobile && !$this->apply):?>
	<?php echo $this->mobile_data ?>
	
<?php else:?>
	<?php echo $this->body_data ?>
<?php endif;?>