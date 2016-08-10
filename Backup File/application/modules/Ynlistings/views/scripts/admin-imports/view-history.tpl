<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h2>
	<?php echo $this->translate("Import History") ?>
</h2>

<?php if( count($this->paginator) ): ?>
<table class='admin_table'>
  <thead>
    <tr>
      <th><?php echo $this->translate("Date") ?></th>
	  <th><?php echo $this->translate("Number of listing") ?></th>
	  <th><?php echo $this->translate("File name") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <?php
      		$viewer = Engine_Api::_() -> user() -> getViewer();
      		$creation = strtotime($item->creation_date);
		    $oldTz = date_default_timezone_get();
		    date_default_timezone_set($viewer->timezone);
		    $creation = date('Y-m-d H:i:s', $creation);
		    date_default_timezone_set($oldTz);
      ?>
      <tr>
        <td><?php echo $creation ?></td>
        <td><?php echo $item->number_listings ?></td>
		<td><?php echo $item->file_name ?></td>
        <td>
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynlistings', 'controller' => 'imports', 'action' => 'view-listing', 'id' => $item->getIdentity()),
                $this->translate("View Listings"),
                array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br/>
<div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
 </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no import entries yet.") ?>
    </span>
  </div>
<?php endif; ?>