<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: statistics.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->pageCount > 1): ?>
  <ul class="paginationControl">
    <?php if (isset($this->previous)): ?>
    <li>
     <a href="javascript:void(0)" onclick="stat_pagination(<?php echo $this->previous;?>)"><?php echo $this->translate('&#171; Previous');?></a>
   	</li>
    <?php endif; ?>
		
    <?php foreach ($this->pagesInRange as $page): ?>
      <li class="<?php if ($page == $this->current): ?>selected<?php endif; ?>" >
        <a onclick="stat_pagination(<?php echo $page;?>)" href="javascript:void(0)"><?php echo $page; ?></a>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->next)): ?>
    	<li>
        <a href="javascript:void(0)" onclick="stat_pagination(<?php echo $this->next;?>)"><?php echo $this->translate('Next &#187;');?></a>
      </li>
    <?php endif; ?>
  </ul>
<?php endif; ?>