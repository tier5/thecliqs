<h2>
	<?php echo $this->translate("Listings which were imported successfully") ?>
</h2>

<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Title") ?></th>
	  <th><?php echo $this->translate("Listing Owner") ?></th>
	  <th><?php echo $this->translate("Category") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->list_listings as $id): ?>
      <?php $item = Engine_Api::_()->getItem('ynlistings_listing', $id) ;?>
      <?php if($item) :?>
      <tr>
        <td><a href='<?php echo $item->getHref();?>'><?php echo $item->title ?></a></td>
        <td><a href='<?php echo $item->getOwner()->getHref();?>'><?php echo $item->getOwner()->displayname ?></a></td>
		<td><?php echo $item->getCategory()->title ?></td>
      </tr>
      <?php else:?>
      	<td><?php $this->translate('Deleted Listing');?></td>
      	<td></td>
      	<td></td>
      <?php endif;?>
    <?php endforeach; ?>
  </tbody>
</table>
