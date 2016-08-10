<script type="text/javascript">

function changeOrder(listby, default_direction){
    var currentOrder = '<?php echo $this->formValues['order'] ?>';
    var currentOrderDirection = '<?php echo $this->formValues['direction'] ?>';
    // Just change direction
    if( listby == currentOrder ) {
        $('direction').value = ( currentOrderDirection == 'ASC' ? 'DESC' : 'ASC' );
    } 
    else {
        $('order').value = listby;
        $('direction').value = default_direction;
    }
    $('filter_form').submit();
}


function selectAll() {
    var i;
    var multidelete_form = $('multidelete_form');
    var inputs = multidelete_form.elements;
    for (i = 1; i < inputs.length; i++) {
        if (!inputs[i].disabled) {
            inputs[i].checked = inputs[0].checked;
        }
    }
}

function deleteSelected(){
    var checkboxes = $$('td input.checkbox[type=checkbox]');
    var selecteditems = [];
    checkboxes.each(function(item){
        var checked = item.checked;
        var value = item.value;
        if (checked == true && value != 'on'){
            selecteditems.push(value);
        }
    });
    $('multidelete').action = en4.core.baseUrl +'admin/ynmusic/artists/multidelete';
    $('ids').value = selecteditems;
    $('multidelete').submit();
}
</script>
<h2><?php echo $this->translate("YouNet Music Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<h3><?php echo $this->translate("Manage Artists") ?></h3>
<br/>
<div class="add_link">
<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynmusic', 'controller' => 'artists', 'action' => 'create'),
    '<i class="fa fa-plus-square"></i>'.$this->translate('Add Artist'), 
    array('class' => '')); ?>
</div>
<br/>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br/>
<?php if( $this->paginator -> getTotalItemCount() ): ?>
<form id='multidelete' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='multidelete_form' class="yn_admin_form" method="post" action="<?php echo $this->url();?>">
    <table class='admin_table'>
        <thead>
            <tr>
                <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('title', 'ASC');"><?php echo $this->translate("Name") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('country', 'ASC');"><?php echo $this->translate("Country") ?></a></th>
                <th><?php echo $this->translate("Genre") ?></th>
                <th><?php echo $this->translate("No of Songs") ?></th>
                <th><?php echo $this->translate("No of Albums") ?></th>
                <th><?php echo $this->translate("No of Playlist") ?></th>
                <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
            <tr>
                <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
                <td><?php echo $item ?></td>
	            <td><?php echo $item->country ?></td>
	            <?php 
	            	// get mapping table
					$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
					// Delete all genre with artist in mapping table
					$genres = $genreMappingsTable -> getGenresByItem($item);
					
				    $arrayGenre = array();
	             	foreach ($genres as $genre) {
	             	  	 $genre = Engine_Api::_() -> getItem('ynmusic_genre', $genre -> genre_id);
						 if($genre) {
	             	  	 	$arrayGenre[] = $genre -> getTitle();
						 } 
	             	}
					
	            ?>
	            <td>
	            	<?php 
	            		if(!empty($arrayGenre)){
	            			echo implode(" | ",$arrayGenre);
	            		}
	            	?>
	            </td>
	            <td><?php echo $item -> getCountItems('ynmusic_song');?></td>
	            <td><?php echo $item -> getCountItems('ynmusic_album');?></td>
	            <td><?php echo $item -> getCountItems('ynmusic_playlist');?></td>
	            <td>
	                <a href="<?php echo 
	                    $this->url(array('module' => 'ynmusic', 'controller' => 'artists', 'action' => 'edit', 'id' => $item -> getIdentity()), 'admin_default') ?>"><?php echo $this->translate('Edit') ?> </a>
	                |
	                <a class="smoothbox" href="<?php echo 
	                    $this->url(array('module' => 'ynmusic', 'controller' => 'artists', 'action' => 'delete', 'id' => $item -> getIdentity()), 'admin_default') ?>"><?php echo $this->translate('Delete') ?></a>
	            </td>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>
<br/>
<?php if ($this->paginator -> getTotalItemCount()) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<br/>
<div class='buttons'>
    <button type='button' onclick="deleteSelected()"><?php echo $this->translate('Delete Selected') ?></button>
</div>

<br/>
<div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?>
</div>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate("There are no artists.") ?></span>
</div>
<?php endif; ?>

