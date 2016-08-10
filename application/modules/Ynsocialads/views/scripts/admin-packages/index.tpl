<script type="text/javascript">

en4.core.runonce.add(function(){

    new Sortables('demo-list', {
      contrain: false,
      clone: true,
      handle: 'span',
      opacity: 0.5,
      revert: true,
      onComplete: function(){
        new Request.JSON({
          url: '<?php echo $this->url(array('controller'=>'packages','action'=>'sort'), 'admin_default') ?>',
          noCache: true,
          data: {
            'format': 'json',
            'order': this.serialize().toString(),
            'page' : <?php if($this->page) echo $this->page; else echo '1';?>,
          }
        }).send();
      }
    });
    
});

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
    $('action_selected').action = en4.core.baseUrl +'admin/ynsocialads/packages/' + actionType + '-selected';
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
	<?php echo $this->translate("YNSOCIALADS_MANAGE_PACKAGE_DESCRIPTION") ?>
</p>
<div class="add_link">
	<?php echo $this->htmlLink(
	array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'packages', 'action' => 'create'),
	$this->translate('Add New Packgage'), 
	array('class' => 'buttonlink add_adblock')) ?>
</div>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<br />
<?php if( count($this->paginator) ): ?>
<form style="position: relative;" id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table ynsocial_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('package_id', 'DESC');"><?php echo $this->translate("ID") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'DESC');"><?php echo $this->translate("Package Title") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('price', 'DESC');"><?php echo $this->translate("Price") ?></a></th>
      <th><a href="javascript:void(0);" onclick="javascript:changeOrder('benefit_type', 'DESC');"><?php echo $this->translate("Benefit") ?></a></th>
	  <th><?php echo $this->translate("Allowed Ad Types") ?></th>
	  <th><a href="javascript:void(0);" onclick="javascript:changeOrder('show', 'DESC');"><?php echo $this->translate("Show") ?></a></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody id="demo-list">
    <?php foreach ($this->paginator as $item): ?>
      <tr id='package_item_<?php echo $item->getIdentity() ?>'>
        <td class="ynsocialads_check"><input type='checkbox' class='checkbox' value='<?php echo $item->getIdentity() ?>' /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td> 
        	<a class='smoothbox' 
	        	href="<?php echo $this->url(
					array(
						'action' => 'view-package',
						'id' => $item->getIdentity(),
					), 
					'ynsocialads_ads', 
					true
				);?>">
			 	<?php echo $item->title ?>
			</a>
		</td>
        <td><?php echo $this->locale() -> toCurrency($item->price, $item->currency) ?></td>
		<td><?php echo $item->benefit_amount." ".$item->benefit_type."s"  ?></td>
		<td>
			<?php $i =1; foreach($item->allowed_ad_types as $types) :?>
				<?php 
					if($i == count($item->allowed_ad_types)){
						echo $types ;
					}
					else {
						echo $types.", " ;
					}
					$i++;
				?>
			<?php endforeach;?>
		</td>
		<td><?php if($item->show == 1) echo $this->translate("Show"); else echo $this->translate("Hide"); ?></td>
        <td>
          <!-- delete --> 
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'packages', 'action' => 'delete', 'id' => $item->getIdentity()),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
          <?php 
          	  $count = Engine_Api::_() -> getItemTable('ynsocialads_ad') -> countAdsPackage($item->getIdentity());
		  ?>      
		  <?php if($count == 0) :?>
           <!-- edit --> 
          |  	
          <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynsocialads', 'controller' => 'packages', 'action' => 'edit', 'id' => $item->getIdentity()),
                $this->translate("edit")) ?>
          <?php endif;?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

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
 <div class='buttons'>
 <button type='button' onclick="javascript:actionSelected('delete');"><?php echo $this->translate("Delete Selected") ?></button>
 </div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no packages yet.") ?>
    </span>
  </div>
<?php endif; ?>
