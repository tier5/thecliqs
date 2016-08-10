<script type="text/javascript">

function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
      // Just change direction
      if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
      } else {
        $('order').value = listby;
        $('direction').value = default_direction;
      }
      $('filter_form').submit();
    }

function actionSelected(actionType){
    var checkboxes = $$('td.ynsocialads_check input[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
      var checked = item.checked;
      var value = item.value;
      if (checked == true && value != 'on'){
        selecteditems.push(value);
      }
    });
    $('action_selected').action = en4.core.baseUrl +'admin/ynsocialads/ads/' + actionType + '-selected';
    $('ids').value = selecteditems;
    $('action_selected').submit();
  }



function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2>
  <?php echo $this->translate('SocialAds Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
	<?php echo $this->translate("YNSOCIALADS_MANAGE_AD_DESCRIPTION") ?>
</p>

<br />

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<div class='admin_table_form'>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.ad_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.name', 'DESC');"><?php echo $this->translate("Name") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.status', 'DESC');"><?php echo $this->translate("Status") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.approved', 'DESC');"><?php echo $this->translate("Approved?") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.deleted', 'DESC');"><?php echo $this->translate("Deleted?") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('campaign.title', 'DESC');"><?php echo $this->translate("Campaign") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'DESC');"><?php echo $this->translate("Advertiser") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.start_date', 'DESC');"><?php echo $this->translate("Start Date") ?></a></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.end_date', 'DESC');"><?php echo $this->translate("End Date") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('ads.ad_type', 'DESC');"><?php echo $this->translate("Type") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td class="ynsocialads_check"><input type='checkbox' class='checkbox' value='<?php echo $item->getIdentity() ?>' /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $this->htmlLink($item->getHref(), $this->translate($item->name), array()) ?></td>
        <td><?php echo ucfirst($this -> translate($item->status)) ?></td>
		<td><?php echo ($item->approved == 1)?$this -> translate("Yes"): $this -> translate("No")?></td>
		<td><?php echo $item->deleted?$this -> translate("Yes"): $this -> translate("No") ?></td>
		<td><?php echo $item->getCampaignName()?></td>
		<td><?php echo $item->getOwner() ?></td>
		<td><?php echo $this->locale()->toDate($item->start_date) ?></td>
		<td><?php echo $this->locale()->toDate($item->end_date) ?></td>
		<td><?php echo $item->ad_type ?></td>
        <td>
          <!-- delete --> 
          <?php if($item->status != "deleted"):?>  
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Delete', 'id' => $item->getIdentity()),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>
           <!-- edit --> 
          <?php if(($item->status == "draft" || $item->status == "unpaid") && $item->isEditable()) :?>
          <?php if(!$item->isPayLater() && $item->isEditable()) :?>
          |  	
          <?php echo $this->htmlLink(
                array('route' => 'ynsocialads_ads', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'edit', 'id' => $item->getIdentity()),
                $this->translate("edit"),
                array()) ?>
          <?php endif;?>     
          <?php endif;?>   
          <!-- approve --> 
          <?php if(($item->approved == 0) && ($item->status != "draft") && ($item->status != "deleted") && ($item->status != "unpaid")):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Approve', 'id' => $item->getIdentity()),
                $this->translate("approve"),
                array('class' => 'smoothbox')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Deny', 'id' => $item->getIdentity()),
                $this->translate("deny"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>  
          <!-- pause -->  
           <?php if($item->status == "running"):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Pause', 'id' => $item->getIdentity()),
                $this->translate("pause"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>      
          <!-- resume -->  
           <?php if($item->status == "paused"):?>  
          |      
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'ads', 'action' => 'update-status', 'status' => 'Resume', 'id' => $item->getIdentity()),
                $this->translate("resume"),
                array('class' => 'smoothbox')) ?>
          <?php endif;?>            
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
</div>
</form>
<br/>
  <form id='action_selected' method="post" action="">
   		<input type="hidden" id="ids" name="ids" value=""/>
  </form>
<div>
    <?php  echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => false,
      'query' => $this->formValues,
    ));     ?>
 </div>
</div>
<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no ads entries yet.") ?>
    </span>
  </div>
<?php endif; ?>
