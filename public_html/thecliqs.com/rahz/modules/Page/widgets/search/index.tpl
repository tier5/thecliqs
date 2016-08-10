<script type="text/javascript">
page_search.page_id = <?php echo (int)$this->subject->getIdentity(); ?>;
page_search.url = "<?php echo $this->url(array('page_id' => $this->subject->getIdentity()), 'page_search'); ?>";
page_search.filter_url = "<?php echo $this->url(array('page_id' => $this->subject->getIdentity(), 'action' => 'filter'), 'page_search'); ?>";
page_search.tag_url = "<?php echo $this->url(array('page_id' => $this->subject->getIdentity(), 'action' => 'tag'), 'page_search'); ?>";
en4.core.runonce.add(function(){
	page_search.init();
});
</script>

<?php
  $this->headTranslate('Search');
?>

<div class="page-search-container">
	<div class="page-search-form">
		<div class="page-search-input">
			<input type="text" ondblclick="page_search.more();" class="page-search-field inactive" id="page-search-field" value="<?php echo $this->translate('Search'); ?>" />
		</div>
		<div class="page-search-loader" id="page-search-loader"></div>
		<div class="clr"></div>
    <div class="description">
      <?php echo $this->translate('Advanced Search - double click in input');?>
    </div>
	</div>
</div>

<div class="hidden" id="page-search-filter-form">
	<div class="search-tab-filter">
		<div class="search">
			<form method="post" action="/405/search-pages/5" onsubmit="page_search.filter(this); return false;" class="global_form_box" enctype="application/x-www-form-urlencoded" id="search_filter_form">
				<div class="form-element keyword">
					<input type="text" value="<?php echo $this->keyword; ?>" id="search-keyword" name="keyword">
				</div>
				<div class="form-element content">
					<?php foreach ($this->enabledModules as $module => $value): ?>
						<div class="element">
							<input type="checkbox" CHECKED value="1" id="content-<?php echo $module; ?>" name="content[<?php echo $module; ?>]" />
							<label class="optional" for="content-<?php echo $module; ?>"><?php echo $this->translate($this->labels[$module]); ?></label>
							<div class="clr"></div>
						</div>
					<?php endforeach; ?>
					<div class="clr"></div>
				</div>
				<div class="form-element submit">
					<div class="buttons">
						<button type="submit" id="search" name="search"><?php echo $this->translate("Search"); ?></button>
					</div>
				</div>
				<div class="clr"></div>
			</form>
		</div>
	</div>
</div>