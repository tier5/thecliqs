<?php 
$this->headScript()
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/picker/Locale.en-US.DatePicker.js')
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/picker/Picker.js')
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/picker/Picker.Attach.js')
	->appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynultimatevideo/externals/scripts/picker/Picker.Date.js');
	$this->headLink()
		->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Ynultimatevideo/externals/styles/picker/datepicker_dashboard.css');
?>
<h2>
    <?php echo $this->translate('Ultimate Video Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Migrate Videos') ?></h3>

<p><?php echo $this->translate("YNULTIMATEVIDEO_ADMIN_MIGRATE_DESCRIPTION") ?></p>

<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'migration-video', 'action' => 'report'),
    '<i class="fa fa-history"></i>'.$this->translate('View Report'),
   	array('class' => 'view-report')
)?>

<ul class="item-tab">
	<li id="item-tab-album"><a href="<?php echo $this->url(array('module'=>'ynultimatevideo','controller'=>'migration-video'), 'admin_default', true)?>"><?php echo $this->translate('Videos')?></a></li>
	<li id="item-tab-playlist" class="active"><a href="<?php echo $this->url(array('module'=>'ynultimatevideo','controller'=>'migration-video','action'=>'playlist'), 'admin_default', true)?>"><?php echo $this->translate('Playlists')?></a></li>
</ul>

<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>

<?php if( count($this->paginator) ): ?>
<form id='multiselect' method="post" action="">
    <input type="hidden" id="ids" name="ids" value=""/>
</form>
<form id='multiselect_form' class="ynadmin-table" method="post" action="<?php echo $this->url();?>">
    <table class='admin_table'>
        <thead>
            <tr>
                <th class='admin_table_short'><input id="check_all" onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('object.title', 'ASC');"><?php echo $this->translate("Playlist") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('user.displayname', 'ASC');"><?php echo $this->translate("Owner") ?></a></th>
                <th><a href="javascript:void(0);" onclick="javascript:changeOrder('object.creation_date', 'ASC');"><?php echo $this->translate("Created") ?></a></th>
                <th style="width: 5%"><a href="javascript:void(0);" onclick="javascript:changeOrder('object.video_count', 'ASC');"><?php echo $this->translate("Number of videos") ?></a></th>
                <th style="width: 10%"><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody id="import-items">
        <?php foreach ($this->paginator as $item): ?>
            <tr id="import-item_<?php echo $item->getIdentity()?>">
                <td><input type='checkbox' class='checkbox' name='select_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getGuid(); ?>" /></td>
                <td><?php echo $item; ?></td>
                <td><?php echo $item->getOwner(); ?></td>
                <td><?php echo $this->locale()->toDate($item->creation_date); ?></td>
                <td><?php echo $item -> video_count; ?></td>
                <td>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynultimatevideo', 'controller' => 'migration-video', 'action' => 'import', 'item' => $item->getGuid()),
                    $this->translate('import'),
                   	array('class' => 'smoothbox')
                )?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>
<?php if (count($this->paginator)) {
    echo '<p class=result_count>';
    $total = $this->paginator->getTotalItemCount();
    echo $this->translate(array('Total %s result', 'Total %s results', $total),$total);
    echo '</p>';
}?>
<div class='buttons'>
    <button type='button' onclick="importSelected()"><?php echo $this->translate('Import Selected') ?></button>
</div>

<br/>
<div><?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues,
    )); ?></div>
<?php else: ?>
	<br/>
<div class="tip">
    <span><?php echo $this->translate("There are no playlists can import.") ?></span>
</div>
<?php endif; ?>

<script type="text/javascript">
	window.addEvent('domready', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            onSelect: function(date){
            }
        });
    });
    
	function selectAll() {
	    var i;
	    var multiselect_form = $('multiselect_form');
	    var inputs = multiselect_form.elements;
	    for (i = 1; i < inputs.length; i++) {
	        if (!inputs[i].disabled) {
	            inputs[i].checked = inputs[0].checked;
	        }
	    }
	}
	
	function importSelected(){
	    var checkboxes = $$('td input.checkbox[type=checkbox]');
	    var selecteditems = [];
	    checkboxes.each(function(item){
	        var checked = item.checked;
	        var value = item.value;
	        if (checked == true && value != 'on'){
	            selecteditems.push(value);
	        }
	    });
	    if (selecteditems.length == 0) {
	    	var div = new Element('div', {
	    		'class': 'warning-wrapper'
	    	});
	    	var p = new Element('p', {
	    		'class': 'warning-message',
	    		text: '<?php echo $this->translate('Please select at least one item for importing.')?>'
	    	})
	    	var btn = new Element('button', {
	    		'class': 'warning-btn close',
	    		text: '<?php echo $this->translate('Close')?>',
	    		onclick: 'parent.Smoothbox.close();'
	    	})
	    	div.adopt(p, btn);
	    	Smoothbox.open(div);
	    }
	    else {
	    	var url = '<?php echo $this->url(array('module'=>'ynultimatevideo','controller'=>'migration-video','action'=>'multiimport'), 'admin_default', true)?>/items/'+selecteditems.join(',');
			Smoothbox.open(url);
	    }
	}
	
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
</script>
