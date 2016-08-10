<script type="text/javascript">
en4.core.runonce.add(function(){
    $$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){
        var checked = $(this).checked;
        var checkboxes =$$('td.ynlistings_check input[type=checkbox]');
        checkboxes.each(function(item,index){
        item.checked = checked;
       });
    })
});

function actionSelected(actionType){
    var checkboxes = $$('td.ynlistings_check input[type=checkbox]');
    var selecteditems = [];

    checkboxes.each(function(item){
    	var checked = item.checked;
        var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/ynlistings/report/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }

</script>
<h2>
  <?php echo $this->translate("Ynlistings Plugin") ?>
</h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu

      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<p>
	<?php
		$description = $this->translate('REPORTS_VIEW_LISTINGS_ADMINMANAGE_INDEX_DESCRIPTION');
		$description = vsprintf($description, array(
	      'http://' . $_SERVER['HTTP_HOST'] . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
	          'controller' => 'report',
	        ), 'admin_default', true),
	    ));
    ?>
  <?php echo $this->translate($description) ?>
</p>
<br />
<?php if( count($this->paginator) ): ?>
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input type='checkbox' class='checkbox' /></th>
        <th><?php echo $this->translate("ID") ?></th>
        <th><?php echo $this->translate("Listing Title") ?></th>
        <th><?php echo $this->translate("Topic Title") ?></th>
        <th><?php echo $this->translate("Post ID")?> </th>
        <th><?php echo $this->translate("Reporter") ?></th>
        <th style='width:200px;'><?php echo $this->translate("Report") ?></th>
        <th><?php echo $this->translate("Date") ?></th>
        <th><?php echo $this->translate("Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td class="ynlistings_check"><input type='checkbox' class='checkbox' value='<?php echo $item->report_id ?>' /></td>
          <td><?php echo $item->report_id ?></td>
          <td>
          	<?php
          		$listingTable = Engine_Api::_()->getItemTable('ynlistings_listing');
          		$listingName = $listingTable ->info('name');
          		$select = $listingTable->select()->where("listing_id = $item->listing_id");
          		$listing_title = $listingTable->fetchAll($select);
          		if(count($listing_title)==1){
          			echo $listing_title[0]['title'];
          		}
          		else
          		{
          			echo $this->translate('N/A');
          		}
          	?></td>
          <td>
          <?php
          		$topicTable = Engine_Api::_()->getItemTable('ynlistings_topic');
          		$topicName =$topicTable ->info('name');
          		$select = $topicTable->select()->where("topic_id = $item->topic_id");
          		$topic = $topicTable->fetchAll($select);
          		if(count($topic)==1){
          			echo $topic[0]['title'];
          		}
          		else
          		{
          			echo $this->translate('N/A');
          		}
          	?></td>
          <td><?php if($item->post_id)
          {
          	echo $item->post_id;
          }
          else echo $this->translate('N/A');
          ?></td>

          <td><?php echo $this->user($item->user_id)->getTitle(); ?></td>
          <td><?php echo $this->viewMore($item->content); ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td>
          <?php
          		if(count($topic)==0){
          ?>

            <a href="<?php echo $this->url(array('controller'=>'index', 'action'=>'view', 'id' => $item->listing_id), 'ynlistings_general') ?>">
             	<span><?php echo $this->translate("view") ?></span>
            </a>

            <?php
          		}
            	else {
            	if($item->post_id)
            		{
            			$postTable = Engine_Api::_()->getItemTable(('ynlistings_post'));
            			$postName = $postTable->info('name');
            			$select = $postTable->select()->where("post_id = $item->post_id");
            			$post = $postTable->fetchAll($select);
            			if(count($post)==1) echo  $this->htmlLink($post[0]->getHref(),$this->translate('view'));
            		}
					//view topic
            	else echo $this->htmlLink($topic[0]->getHref(), $this->translate("view"));
            	}

            ?>
            |
            <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'ynlistings', 'controller' => 'admin-report', 'action' => 'delete', 'id' => $item->report_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <br />
  <div class='buttons'>
    <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
  </div>
  <br />
  <form id='action_selected' method="post" action="">
       <input type="hidden" id="ids" name="ids" value=""/>
  </form>
  <div>
    <?php echo $this->paginationControl($this->paginator); ?>
  </div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no reports posted by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
