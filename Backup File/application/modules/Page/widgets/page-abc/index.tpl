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
  <div class="page-abc-wrapper" id="page-abc-wrapper">
    <a href="<?php echo $this->url(array('sort_type'=>'abc','sort_value'=>''), 'page_browse_sort'); ?>"
       onclick='page_manager.setAbc(""); return false;'
       id="page-abc-" <?php if( $this->abc_active == '') :?> class='active' <?php endif; ?>>
       <?php echo $this->translate('PAGE_ABC_All'); ?></a>
    <?php foreach($this->abc as $abc): ?>
      <a href="<?php echo $this->url(array('sort_type'=>'abc','sort_value'=>$abc), 'page_browse_sort'); ?>"
        onclick='page_manager.setAbc("<?php echo $abc; ?>"); return false;'
        id="page-abc-<?php echo $abc; ?>" <?php if($abc == $this->abc_active) :?> class='active' <?php endif; ?>>
        <?php echo $abc; ?></a>
    <?php endforeach; ?>
    <a href="<?php echo $this->url(array('sort_type'=>'abc','sort_value'=>'#'), 'page_browse_sort'); ?>"
       onclick='page_manager.setAbc("#"); return false;'
       id="page-abc-#" <?php if( $this->abc_active == '#') :?> class='active' <?php endif; ?>>
       <?php echo $this->translate('PAGE_ABC_Num'); ?></a>
  </div>
</div>
