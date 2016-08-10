<script type="text/javascript">

// function featureSong(obj, id) {
    // var value = (obj.checked) ? 1 : 0;
    // var url = en4.core.baseUrl+'admin/ynmusic/songs/feature';
    // new Request.JSON({
        // url: url,
        // method: 'post',
        // data: {
            // 'id': id,
            // 'value': value
        // }
    // }).send();
// }

// function featureAll() {
    // var hasElement = false;
    // var i;
    // var multiselect_form = $('multidelete_form');
    // var inputs = multiselect_form.elements;
    // for (i = 1; i < inputs.length; i++) {
        // if (!inputs[i].disabled && inputs[i].hasClass('featured_checkbox')) {
            // inputs[i].checked = $('check_all_feature').checked;
            // featureSong(inputs[i], inputs[i].getProperty('data_id'));
        // }
    // }
// }


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
    $('multidelete').action = en4.core.baseUrl +'admin/ynmusic/songs/multidelete';
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

<h3><?php echo $this->translate("Manage Songs") ?></h3>
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
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('song.title', 'ASC');"><?php echo $this->translate("Name") ?></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate("Added by") ?></a></th>
                <th><?php echo $this->translate("Artists") ?></th>
                <th><?php echo $this->translate("Genre") ?></th>
                <th><?php echo $this->translate("Albums") ?></th>
                <th><?php echo $this->translate("Plays") ?></th>
                <!-- <th><input id="check_all_feature" onclick='featureAll();' type='checkbox' class='checkbox' /><?php echo $this->translate("Featured") ?></th> -->
                <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
            <tr>
                <td><input type='checkbox' class='checkbox' <?php if (!$item->isDeletable()) echo 'disabled'?> name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
                <td><?php echo $item ?></td>
	            <td><?php if($item -> user_id != 0) echo $item->getOwner(); ?></td>
	            <td>
	            	<?php 
	            		$artistMappingsTable = Engine_Api::_() -> getDbTable('artistmappings', 'ynmusic');
						$artistMappings = $artistMappingsTable -> getArtistsByItem($item);
						$artistValues = array();
						foreach($artistMappings as $artistMapping) {
							$artist = Engine_Api::_() -> getItem('ynmusic_artist', $artistMapping -> artist_id);
							if($artist) {
								$artistValues[$artistMapping -> artist_id] = $artist -> getTitle();
							}
						}
						if(count($artistValues)) {
							echo implode(" | ",$artistValues);
						}
	            	?>
	            </td>
	            <td>
	            	<?php 
	            		$genreMappingsTable = Engine_Api::_() -> getDbTable('genremappings', 'ynmusic');
						$genreMappings = $genreMappingsTable -> getGenresByItem($item);
						$genreValues = array();
						foreach($genreMappings as $genreMapping) {
							$genre = Engine_Api::_() -> getItem('ynmusic_genre', $genreMapping -> genre_id);
							if($genre) {
								$genreValues[$genreMapping -> genre_id] = $genre -> getTitle();
							}
						}
						if(count($genreValues)) {
							echo implode(" | ",$genreValues);
						}
	            	?>
	            </td>
	            <td><?php echo $item -> getAlbum();?></td>
	            <td><?php echo $item -> play_count; ?></td>
	             <!-- <td><input <input data_id = '<?php echo $item->getIdentity()?>' type="checkbox" class="featured_checkbox" value="1" onclick="featureSong(this, '<?php echo $item->getIdentity()?>')" <?php if ($item->featured) echo 'checked'?>/></td> -->
	            <td class="option-link">
	            	<?php if ($item->isEditable()) :?>
            		<a href="<?php echo $this->url(array('action' => 'edit', 'song_id' => $item -> getIdentity()), 'ynmusic_song', true) ?>"><?php echo $this->translate('Edit') ?></a>
		            <?php endif;?>
		            <?php if ($item->isDeletable()) :?>
	            		<a class="smoothbox" href="<?php echo $this->url(array('action' => 'delete', 'id' => $item -> getIdentity()), 'ynmusic_song', true) ?>"><?php echo $this->translate('Delete') ?></a>
		            <?php endif;?>
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
    <span><?php echo $this->translate("There are no songs.") ?></span>
</div>
<?php endif; ?>

