<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _countryList.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->list_items->getCurrentItemCount() > 0): ?>
<div class="stat_locations">
	<table class="page_table">
		<tr>
			<td class="header">#</td>
			<td class="header"><?php echo $this->translate("Country/Territory"); ?></td>
			<td class="header"><?php echo $this->translate("Visitors"); ?></td>
			<td class="header"><?php echo $this->translate("Percentage"); ?></td>
		</tr>
		<?php $counter = 0; ?>
		<?php foreach ($this->list_items as $item): ?>
		<?php $counter++; ?>
		<tr>
			<td class="item"><?php echo $counter; ?>.</td>
			<td class="item"><?php echo $item['country']; ?></td>
			<td class="item"><?php echo $item['count']; ?></td>
			<td class="item"><?php echo round((($item['count'] * 1.0 / $this->total_items) * 100), 2); ?><span class="page_unit">%</span></td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

<div class="stat_pagination">
	<?php if( $this->list_items->count() > 1 ): ?>
		<?php echo $this->paginationControl($this->list_items, null, array("pagination/statistics.tpl","page")); ?>
	<?php endif; ?>
</div>