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
  en4.core.runonce.add(function(){
    if ($('store_tag_info')) {
      if (!$('filter_form')) {
        store_manager.init();
      }
    } else {
      store_manager.tag_url = '<?php echo $this->url(
        array(
             'action'=>'stores'
        ),
				'store_extended'
      );?>';
    }
  });
</script>

<div class="store-widget">
	<?php foreach($this->tags as $tag): ?>
    <a
      title="<?php echo $tag['text']?>"
      id="tag_<?php echo $tag['tag_id']?>"
      class="he_tag<?php echo $tag['class']?>"
      href="javascript:store_manager.setTag(<?php echo $tag['tag_id']?>)"
    ><?php echo $tag['text']?><sup><?php echo $this->locale()->toNumber($tag['freq'])?></sup></a>
	<?php endforeach; ?>
</div>
