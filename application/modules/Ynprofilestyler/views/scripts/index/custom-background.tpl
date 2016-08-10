<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>
<form id="form-background">
	<div class="col1">
        <?php echo $this->form->getElement('background_color')->render(); ?>
        <?php echo $this->form->getElement('background')->render(); ?>
    </div>
    
    <fieldset class="col2">    
    	<legend><?php echo $this->translate('Or Choose') ?></legend>
    	<div>
    		<input type="checkbox" id="ynprofilestyler-chkbox" />

    		<label><?php
    		    echo $this->translate('Use Image - Enter the image URL')
    		    ?></label>
    	</div>
    	<div id="ynprofilestyler_apply_image">
        	<div class="image">
        	    <?php echo $this->form->getElement('background_image')->renderViewHelper(); ?>
        	    <p>
        	    	<?php echo $this->translate('or')?>
        	    	<a href="javascript:void(0)" 
        	    		onclick="ynps2.openWnd(
        	    			'<?php echo $this->url(array('module' => 'ynprofilestyler', 'action' => 'upload', 'controller' => 'index'))?>',
        	    			'#content-background input[name=background_image]'
    	    			)"><?php echo $this->translate('browse')?></a>
    	    		<?php echo $this->translate('from your computer')?>    	    	
        	    </p>
        	</div>
        	<div class="repeat_position">
        		<?php echo $this->form->getElement('background_position')->render(); ?>
        		<?php echo $this->form->getElement('background_repeat')->render(); ?>
        		<?php echo $this->form->getElement('background_attachment')->render(); ?>        		
        	</div>
    	</div>
    </fieldset>
</form>

<script language="javascript" type="text/javascript">
	var bgEleBg = $('#content-background input[name=background_image]');
	
	ynps2.hideImageArea = function() {
		$('#ynprofilestyler_apply_image').hide();
		var ruleId = $(bgEleBg).attr('rule_id');
		$(bgEleBg).attr('preview', 0);
		ynps2.setRule(ruleId, '');
	}

	ynps2.showImageArea = function() {
		$('#ynprofilestyler_apply_image').show();
		$(bgEleBg).attr('preview', 1);
	}

	ynps2.clearBackgroundHeader = function() {
		if ($(bgEleBg).val() != '' || $('#content-background input[name=background_color][type=hidden]').val() != '') {
			$('#content-background input[name=background]').attr('preview', 1);
		} else {
			$('#content-background input[name=background]').attr('preview', 0);
		}
	}
	
	$('#ynprofilestyler-chkbox').click(function() {	
		if (!$(this).is(':checked')) {		
			ynps2.hideImageArea();
		} else {
			ynps2.showImageArea();
		}		
	});

	if (ynps2.getRuleValue($(bgEleBg).attr('rule_id')) != '') {		
		document.getElementById('ynprofilestyler-chkbox').checked = true;
		ynps2.showImageArea();	
	} else {
		ynps2.hideImageArea();
	}

	$(bgEleBg).change(function() {
		ynps2.clearBackgroundHeader();
	});

	$('#content-background input[name=background_color][type=hidden]').change(function() {
		ynps2.clearBackgroundHeader();
	});
</script>