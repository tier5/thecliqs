<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-05-31 17:53 ulan $
 * @author     Ulan T
 */
?>

<?php if ($this->pageCount > 1): ?>
<ul class="paginationControl">
  <?php if (isset($this->previous)): ?>
  <li>
    <a href="" onclick="Pagination.getPage(<?php echo $this->previous;?>); return false;">
      <?php echo $this->translate('&#171; Previous');?>
    </a>
  </li>
  <?php endif; ?>

  <?php if (isset($this->next)): ?>
  <li>
    <a href="" onclick="Pagination.getPage(<?php echo $this->next;?>); return false;"><?php echo $this->translate('Next &#187;');?></a>
  </li>
  <?php endif; ?>
</ul>
<?php endif; ?>