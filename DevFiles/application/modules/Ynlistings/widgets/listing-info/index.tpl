<?php $this -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>	

<div class="rich_content_body">
<?php echo $this->translate($this->listing->description) ?>
</div>

<?php $fieldStructure = Engine_Api::_() -> fields() -> getFieldsStructurePartial($this -> listing); ?>
<?php if($this -> fieldValueLoop($this -> listing, $fieldStructure)):?>
<h3><?php echo $this -> translate('Listing Specifications'); ?> </h3>
	<div class="listing_title">
	       <?php echo $this -> fieldValueLoop($this -> listing, $fieldStructure); ?>
	</div>
<?php endif; ?>
