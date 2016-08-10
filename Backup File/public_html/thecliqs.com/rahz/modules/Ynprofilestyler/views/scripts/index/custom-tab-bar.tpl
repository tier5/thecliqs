<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<form id="form-tab-bar">
	<div class="col1">
        <table>
			<tr>
				<td><input type="radio" name="use_color_radio" value="color" checked="checked" /></td>
				<td><?php echo $this->translate('Use Color')?></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="one_row_area">
				        <?php echo $this->form->getElement('background_color')->render(); ?>
			        </div>
			    </td>
			</tr>
			<tr>
				<td colspan="2" class="cell_spacing"></td>
			</tr>
			<tr>
				<td><input type="radio" name="use_color_radio" value="image" /></td>
				<td>
				    <?php echo $this->form->getElement('background_image')->render(); ?>
				    <?php echo $this->translate('or Enter URL')?>
				    <p>
				    	<input type="text" name="background_image" style="width:182px" class="rule-element"
					    	rule_id="<?php echo $this->form->getElement('background_image')->getAttrib('rule_id')?>" />
            	    	<a href="javascript:void(0)"
            	    		onclick="ynps2.openWnd(
            	    			'<?php echo $this->url(array('module' => 'ynprofilestyler', 'action' => 'upload', 'controller' => 'index'))?>',
            	    			'#content-tab-bar input[name=background_image][type=text]'
        	    			)"><?php echo $this->translate('browse')?></a>
            	    </p>
			    </td>				
			</tr>
			<tr>
				<td colspan="2" class="cell_spacing"></td>
			</tr>
			<tr>
				<td><input type="radio" name="use_color_radio" value="nobg" /></td>
				<td>
					<?php echo $this->translate('No background')?>
					<?php echo $this->form->getElement('background')->render(); ?>
				</td>
			</tr>
		</table>	
    </div>
    
    <fieldset class="col2">
    	<legend><?php echo $this->translate('Tab Bar Text') ?></legend>
    	<div class="one_row">
    	    <?php echo $this->form->getElement('font_family')->render() ?>
        	<?php echo $this->form->getElement('font_size')->render() ?>
    	</div>
    	
    	<div class="one_row">
        	<?php echo $this->form->getElement('color')->render() ?>
    	</div>
    </fieldset>
</form>

<script language="javascript" type="text/javascript">
	var bgImgEleTab = $('#content-tab-bar select[name=background_image]');
	var bgInputImgEleTab = $('#content-tab-bar input[name=background_image]');
	var bgNoneEleTab = $('#content-tab-bar input[name=background]');
	var bgColorEleTab = $('#content-tab-bar input[name=background_color]');

	$('#content-tab-bar input[name=use_color_radio]').click(function() {
		if ($(this).val() == 'color') {
			ynps2.notPreviewElements([bgImgEleTab, bgNoneEleTab, bgInputImgEleTab]);

			$(bgColorEleTab).attr('preview', 1);
		} else if ($(this).val() == 'image') {			
			ynps2.notPreviewElements([bgColorEleTab, bgNoneEleTab]);

			if ($('#content-tab-bar input[name=background_image][type=text]').val() == '') {
				$(bgImgEleTab).attr('preview', 1);
				$(bgInputImgEleTab).attr('preview', 0);
			} else {
				$(bgImgEleTab).attr('preview', 0);
				$(bgInputImgEleTab).attr('preview', 1);
			}
			
		} else {
			ynps2.notPreviewElements([bgColorEleTab, bgImgEleTab]);
			$(bgNoneEleTab).attr('preview', 1);
		}
	});

	var valueNoneBg = ynps2.getRuleValue($(bgNoneEleTab).attr('rule_id'));
	
	if (valueNoneBg) {
		$("#content-tab-bar input[name=use_color_radio]").filter("[value=nobg]").prop("checked",true);
	} else {
		var valueImgBg = ynps2.getRuleValue($(bgImgEleTab).attr('rule_id'));
		if (!valueImgBg) {			
			$("#content-tab-bar input[name=use_color_radio]").filter("[value=color]").prop("checked",true).trigger('click');
		} else {
			$("#content-tab-bar input[name=use_color_radio]").filter("[value=image]").prop("checked",true);
		}
	}

	// when the user input his image by entering the URL, then the dropdownlist image will now be used to preview
	$('#content-tab-bar input[name=background_image][type=text]').change(function() {
		if ($('#content-tab-bar input[name=use_color_radio]:checked').val() == 'image') {
			if ($(this).val() != '') {
				$(bgImgEleTab).attr('preview', 0);
				$(bgInputImgEleTab).attr('preview', 1);
			} else {
				$(bgImgEleTab).attr('preview', 1);
				$(bgInputImgEleTab).attr('preview', 0);
			}
		}
	});
</script>