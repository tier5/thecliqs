<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */
?>
<script type="text/javascript">
	<!--

  en4.core.runonce.add(function() {
    if($('product_category_info')) {
      if (!$('filter_form') && !$$('.layout_store_product_tags')) {
        product_manager.init();
      }
      $$('.without_listing').setStyle('display', 'none');
    } else {
      $$('.with_listing').setStyle('display', 'none');
    }
  });

	var cat_minimized = new Hash.Cookie('cat_cookie', {duration: 3600});
	var subCats = function($el, cat_id){
		if($('subcats_' + cat_id).style.display == 'none'){
			$('subcats_' + cat_id).style.display = '';
			$el.src="application/modules/Store/externals/images/icons/minus.gif";
      $$('.store-categories')[0].getElement('li.category_'+cat_id).removeClass('on-hover');
			cat_minimized.set( cat_id, 1 );
		} else {
			$('subcats_' + cat_id).style.display = 'none';
			$el.src="application/modules/Store/externals/images/icons/plus.gif";
      $$('.store-categories')[0].getElement('li.category_'+cat_id).addClass('on-hover');
			cat_minimized.set( cat_id, 0 );
		}
	}

/*  var searchProducts = function(key) {
    if( Browser.Engine.trident ) {
      document.getElementById('filter_form').submit();
    } else {
      var form = $('filter_form');
      form.getElementById('profile_type').value = key;
    }
  }*/
	//-->
</script>

<ul class="store-simple-list store-categories">
  <?php if ( count( $this->categories ) > 1): ?>
    <li class="on-hover">
      <?php echo $this->htmlLink($this->url(array('action'=>'products'), 'store_general', true), $this->translate('STORE_All Categories'), array('class' => 'without_listing')); ?>
      <a class="with_listing" href="javascript:product_manager.setCategory(0)"><?php echo $this->translate('STORE_All Categories')?></a>
    </li>
  <?php endif; ?>
  <?php foreach ( $this->categories as $key=>$category ):?>
    <li class="on-hover category_<?php echo $key;?>">
      <script type="text/javascript">
        <!--
          en4.core.runonce.add(function(){
            if(cat_minimized.get(<?php echo $key; ?>) == 1) {
              $('subcats_'+'<?php echo $key; ?>').style.display = '';
              $$('.store-categories')[0].getElement('li.category_<?php echo $key?>').removeClass('on-hover');
              $('icon_'+'<?php echo $key; ?>').src = 'application/modules/Store/externals/images/icons/minus.gif';
            }
          });
        //-->
      </script>
      <img id='icon_<?php echo $key; ?>'
        src="application/modules/Store/externals/images/icons/<?php if( isset($category['children']) && count( $category['children'] ) > 0 ): ?>plus<?php else: ?>minus_disabled<?php endif; ?>.gif"
        <?php if ( isset($category['children']) && count($category['children']) > 0 ): ?> style='cursor: pointer;' onClick="subCats($(this), <?php echo $key; ?>)" <?php endif; ?> border='0' class='icon'/>

      <?php echo $this->htmlLink($this->url(array('action'=>'products', 'cat'=>$key), 'store_general', true), $this->string()->truncate($this->translate($category['label']), 17, '...'), array('class' => 'without_listing')); ?>
      <a id="category_<?php echo $key?>" class="with_listing" href="javascript:product_manager.setCategory(<?php echo $key?>)"><?php echo $this->translate($category['label'])?></a>

      <ul id='subcats_<?php echo $key; ?>' style='display: none;' class="store-simple-sublist">
        <?php if ( isset($category['children']) && count($category['children']) > 0):?>
          <?php foreach ($category['children'] as $key2 => $child): ?>
            <li>
              <?php echo $this->htmlLink($this->url(array('action'=>'products', 'cat'=>$key, 'sub_cat'=>$key2), 'store_general', true), $this->string()->truncate($this->translate($child), 17, '...'), array('class' => 'without_listing')); ?>
              <a id="sub_category_<?php echo $key2?>" class="with_listing" href="javascript:product_manager.setCategory('<?php echo $key?>', '<?php echo $key2?>')"><?php echo $this->translate($child)?></a>
            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>
    </li>
  <?php endforeach; ?>
</ul>
