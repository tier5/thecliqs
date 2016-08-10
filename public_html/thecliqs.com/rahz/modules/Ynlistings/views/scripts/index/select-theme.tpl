<style type="text/css">
	.form-label{
		display: none;
	}
</style>
<form class='global_form_smoothbox' method="post" action="<?php echo ($this->url()) ?>">
<h3><?php  echo $this->translate('Select Themes')?></h3>
<br/>
<?php
$category = $this->listing->getCategory();
if(count($category -> themes) > 0)
{
	//alread category level 1
}
else {
	$main_parent_category = Engine_Api::_()->getItem('ynlistings_category', $category -> getIdentity()) -> getParentCategoryLevel1();
	$category_parent = Engine_Api::_() -> getItem('ynlistings_category', $main_parent_category -> getIdentity());
	$category = $category_parent;
}
echo $this->partial('_post_listings_themes.tpl', array(
		'theme' => $this->listing->theme,
		'category' => $category,
		'canSelectTheme' => $this->can_select_theme,
		'select_theme' => 1,
));
?>
<br/>
<div style="padding-left: 30px;">
	<button type='submit'><?php echo $this->translate("Select") ?></button>
	<?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
	<a href="javascript:void(0)" onclick="parent.Smoothbox.close();">
	<?php echo $this->translate("cancel") ?></a>
</div>
</form>

<script type="text/javascript">
	  window.addEvent('domready', function() {
	  	$$('.item-form-theme-choose').addEvent('click', function(){
	  		this.getElements('input')[0].set('checked','true');
	  	});
	  });
</script>	
