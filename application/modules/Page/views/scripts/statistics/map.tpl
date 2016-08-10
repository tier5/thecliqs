<?php echo $this->render('_page_options_menu.tpl'); ?>

<script type="text/javascript">
var block = false;

function stat_pagination(page) {
	if (block) return ;
	block = true;
	new Request.JSON({
		'url': '<?php echo $this->url(array('action' => 'map', 'page_id' => $this->page->getIdentity())); ?>',
		'method' : 'post',
		'data' : {
			'p' : page,
			'format' : 'json'
		},
		onSuccess: function(response) {
			$('country_list').innerHTML = response.html;
			block = false;
		}
	}).send();
}
</script>

<div class='layout_left' style="width: auto;">
  <?php echo $this->render('_page_stat_tabs.tpl'); ?>
</div>

<div class='layout_middle'>
  <div class="stat_content">
    <div class="stat_filter_desc"><?php echo $this->translate("PAGE_MAP_DESC"); ?></div>
  	<div class="clr"></div>
  </div>
  <div class="clr"></div>

  <?php if($this->map_items->getTotalItemCount()) :?>
    <?php echo $this->render('_googleMapStat.tpl'); ?>
  <?php endif;?>

  <?php if ($this->map_items): ?>
  	<div class="stat_location_info" id="country_list">
  		<?php echo $this->render('_countryList.tpl'); ?>
  	</div>
  <?php endif; ?>
</div>