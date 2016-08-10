<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-11-04 17:07:11 taalay $
 * @author     Taalay
 */
?>

<div class="page-widget">
	<?php foreach($this->tags as $tag): ?>
        <a id="tag_<?php echo $tag['tag_id']?>"
           class="he_tag<?php echo $tag['class']?>"
           href="<?php echo $this->url(array('sort_type'=>'tag', 'sort_value'=>$tag['text']), 'page_browse_sort')?>"
           onclick="page_manager.setTag(<?php echo $tag['tag_id']?>); return false;">
                <?php echo $tag['text']?><sup><?php echo $tag['freq']?></sup>
        </a>
	<?php endforeach; ?>
</div>
