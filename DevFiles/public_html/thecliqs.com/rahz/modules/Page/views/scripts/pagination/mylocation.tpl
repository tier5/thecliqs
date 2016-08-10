<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 Ulan T $
 * @author     Ulan T
 */
?>

<?php if ($this->pageCount > 1): ?>
<ul class="paginationControl my_location_paginaton">
  <?php if (isset($this->previous)): ?>
  <li>
    <a href="javascript:void(0);" onclick="my_location.set_my_location_page('<?php echo $this->previous?>'); return false;" class="buttonlink my_location_previous">
    </a>
  </li>
  <?php endif; ?>

    <li> </li>

    <li class="selected" >
      <select id="my_location_pagination_select" onchange="my_location.set_my_location_page(this.value);">
        <?php foreach($this->pagesInRange as $page) : ?>
            <option value="<?php echo $page?>" label="<?php echo $page?>"><?php echo $page?></option>
        <?php endforeach;?>
      </select>
    </li>
    <li> </li>
  <?php if (isset($this->next)): ?>
  <li>
    <a href="javascript:void(0);" onclick="my_location.set_my_location_page(<?php echo $this->next;?>); return false;" class="buttonlink my_location_next">

    </a>
  </li>
  <?php endif; ?>
</ul>
<?php endif; ?>
