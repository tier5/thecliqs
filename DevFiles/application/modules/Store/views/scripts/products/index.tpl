<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: products.tpl  17.09.11 11:57 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">

function product_pagination(page) {
	new Request.JSON({
		'url': '<?php echo $this->url(array('page_id' => $this->page->getIdentity()), 'store_products'); ?>',
		'method' : 'post',
		'data' : {
			'p' : page,
			'format' : 'json'
		},
    eval: true,
		onSuccess: function(response) {
			$$('.he-items')[0].innerHTML = response.html;
		}
	}).send();
}
</script>

<?php echo $this->render('_editMenu.tpl'); ?>
<div class="headline store">
  <h2><?php echo $this->translate('STORE_Manage Products');?></h2>
  <div class="tabs"><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render(); ?></div>
</div>
<div class="clr"></div>

<p>
  <?php echo $this->translate('STORE_PAGE_PRODUCTS_DESCRIPTION')?>
</p>

<br />

<?php echo $this->render('_store_list_edit.tpl'); ?>