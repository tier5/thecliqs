<ul class="ynbusinesspages-list-most-item">
<?php foreach ($this->paginator as $business):?>
	<?php echo $this->partial('_business_item.tpl', 'ynbusinesspages', array(
		'business' => $business,
		'filter' => ''
	)); ?>
<?php endforeach;?>
</ul>