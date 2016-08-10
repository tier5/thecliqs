<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<form id="form-menu-bar">
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
            	    			'#content-menu-bar input[name=background_image][type=text]'
        	    			)"><?php echo $this->translate('browse')?></a>
            	    </p>

				    <?php echo $this->form->getElement('background_repeat')->render(); ?>
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
    	<legend><?php echo $this->translate('Menu Bar Text') ?></legend>
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
	var bgRepeatEleMenu = $('#content-menu-bar input[name=background_repeat]');
	var bgImgEleMenu = $('#content-menu-bar select[name=background_image]');
	var bgInputImgEleMenu = $('#content-menu-bar input[name=background_image]');
	var bgNoneEleMenu = $('#content-menu-bar input[name=background]');
	var bgColorEleMenu = $('#content-menu-bar input[name=background_color]');

	ynps2.showOrHideBackgroundRepeat = function() {
		if ($(bgImgEleMenu).val() != '' || $(bgInputImgEleMenu).val() != '') {
			$(bgRepeatEleMenu).attr('preview', 1);
		} else {
			$(bgRepeatEleMenu).attr('preview', 0);
		}
	}

	$('#content-menu-bar input[name=use_color_radio]').click(function() {
		if ($(this).val() == 'color') {
			ynps2.notPreviewElements([bgImgEleMenu, bgRepeatEleMenu, bgNoneEleMenu, bgInputImgEleMenu]);

			$(bgColorEleMenu).attr('preview', 1);
		} else if ($(this).val() == 'image') {			
			ynps2.notPreviewElements([bgColorEleMenu, bgNoneEleMenu]);

			if ($('#content-menu-bar input[name=background_image][type=text]').val() == '') {
				$(bgImgEleMenu).attr('preview', 1);
				$(bgInputImgEleMenu).attr('preview', 0);
			} else {
				$(bgImgEleMenu).attr('preview', 0);
				$(bgInputImgEleMenu).attr('preview', 1);
			}
			ynps2.showOrHideBackgroundRepeat();
			
		} else {
			ynps2.notPreviewElements([bgColorEleMenu, bgImgEleMenu, bgRepeatEleMenu]);
			$(bgNoneEleMenu).attr('preview', 1);
		}
	});

	var valueNoneBg = ynps2.getRuleValue($(bgNoneEleMenu).attr('rule_id'));
	
	if (valueNoneBg) {
		$("#content-menu-bar input[name=use_color_radio]").filter("[value=nobg]").prop("checked",true);
	} else {
		var valueImgBg = ynps2.getRuleValue($(bgImgEleMenu).attr('rule_id'));
		if (!valueImgBg) {			
			$("#content-menu-bar input[name=use_color_radio]").filter("[value=color]").prop("checked",true).trigger('click');
		} else {
			$("#content-menu-bar input[name=use_color_radio]").filter("[value=image]").prop("checked",true);
		}
	}

	$(bgImgEleMenu).change(function() {
		if ($('#content-menu-bar input[name=use_color_radio]:checked').val() == 'image') {
			if ($(this).val() != '') {
				$(bgImgEleMenu).attr('preview', 1);
				$(bgInputImgEleMenu).attr('preview', 0);
			} else {
				$(bgImgEleMenu).attr('preview', 0);
				$(bgInputImgEleMenu).attr('preview', 1);
			}
			ynps2.showOrHideBackgroundRepeat();
		}
	});
	
	// when the user input his image by entering the URL, then the dropdownlist image will now be used to preview
	$('#content-menu-bar input[name=background_image][type=text]').change(function() {
		if ($('#content-menu-bar input[name=use_color_radio]:checked').val() == 'image') {
			if ($(this).val() != '') {
				$(bgImgEleMenu).attr('preview', 0);
				$(bgInputImgEleMenu).attr('preview', 1);
			} else {
				$(bgImgEleMenu).attr('preview', 1);
				$(bgInputImgEleMenu).attr('preview', 0);
			}
			ynps2.showOrHideBackgroundRepeat();
		}
	});
</script>