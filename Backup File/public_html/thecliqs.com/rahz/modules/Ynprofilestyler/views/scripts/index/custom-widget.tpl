<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<div class="left co1">
	<fieldset>
		<legend><?php echo $this->translate('Frame') ?></legend>
    	<form id="form-widget-frame">
    		<div class="right">
    			<div class="one_row_area">
            		<?php echo $this->formFrame->getElement('border_color')->render() ?>
            		<?php echo $this->formFrame->getElement('border_style')->render() ?>
            		<?php echo $this->formFrame->getElement('border_width')->render() ?>
        		</div>
    		</div>
    		<div class="left">
    			<div class="one_row_area">
    				<div class="wrapper">
                	    <input type="radio" name="use_color_radio" value="color" checked="checked" />
                		<?php echo $this->formFrame->getElement('background_color')->render() ?>
            		</div>
            		
            		<div class="wrapper">
                		<input type="radio" name="use_color_radio" value="image" />
                		<?php echo $this->formFrame->getElement('background_image')->render() ?>
					    	
					    <div class="form-wrapper browse_image">		
					        <?php echo $this->translate('or URL')?>		  
    					    <input type="text" name="background_image" class="rule-element"
    					    	rule_id="<?php echo $this->formFrame->getElement('background_image')->getAttrib('rule_id')?>" />
                	    	<a href="javascript:void(0)"
                	    		onclick="ynps2.openWnd(
                	    			'<?php echo $this->url(array('module' => 'ynprofilestyler', 'action' => 'upload', 'controller' => 'index'))?>',
                	    			'#content-widget input[name=background_image][type=text]'
            	    			)"><?php echo $this->translate('browse')?></a>
        	    		</div>	
            		</div>
            		
            		<div class="wrapper">
            			<input type="radio" name="use_color_radio" value="nobg" />
            			<?php echo $this->translate('No background')?>
					    <?php echo $this->formFrame->getElement('background')->render(); ?>
				    </div>
        		</div>
    		</div>
    	</form>
	</fieldset>
</div>

<div class="left">
	<fieldset>
		<legend><?php echo $this->translate('Link') ?></legend>
        <form id="form-widget-link">
        	<div class="one_row_area">
        	    <?php echo $this->formLink->getElement('color')->render() ?>
        	</div>
        </form>
    </fieldset>
</div>

<div class="clear"></div>

<div class="left col1_2cols">
	<fieldset>
		<legend><?php echo $this->translate('Header') ?></legend>
		<form id="form-widget-header">
			<div class="font_area">
			    <?php echo $this->formHeader->getElement('font_family')->render() ?>
			    <?php echo $this->formHeader->getElement('font_size')->render() ?>
    			<?php echo $this->formHeader->getElement('color')->render() ?>	
			</div>
			<div class="font_area">
    			<?php echo $this->formHeader->getElement('font_weight')->render() ?>
    			<?php echo $this->formHeader->getElement('font_style')->render() ?>
    			<?php echo $this->formHeader->getElement('text_decoration')->render() ?>
			</div>
		</form>        
    </fieldset>
</div>

<div class="middle">
	<fieldset>
		<legend><?php echo $this->translate('Text') ?></legend>
		<form id="form-widget-text">
			<div class="font_area">
    			<?php echo $this->formText->getElement('font_family')->render() ?>
    			<?php echo $this->formText->getElement('font_size')->render() ?>
    			<?php echo $this->formText->getElement('color')->render() ?>
			</div>
			<div class="font_area">
    			<?php echo $this->formText->getElement('font_weight')->render() ?>
    			<?php echo $this->formText->getElement('font_style')->render() ?>
    			<?php echo $this->formText->getElement('text_decoration')->render() ?>
			</div>
        </form>
    </fieldset>
</div>

<script language="javascript" type="text/javascript">
	var bgImgEleWidget = $('#content-widget select[name=background_image]');
	var bgInputImgEleWidget = $('#content-widget input[name=background_image]');
	var bgNoneEleWidget = $('#content-widget input[name=background][type=hidden]');
	var bgColorEleWidget = $('#content-widget input[name=background_color]');

	$('#content-widget input[name=use_color_radio]').click(function() {
		if ($(this).val() == 'color') {
			ynps2.notPreviewElements([bgImgEleWidget, bgNoneEleWidget, bgInputImgEleWidget]);

			$(bgColorEleWidget).attr('preview', 1);
		} else if ($(this).val() == 'image') {			
			ynps2.notPreviewElements([bgColorEleWidget, bgNoneEleWidget]);

			if ($('#content-widget input[name=background_image][type=text]').val() == '') {
				$(bgImgEleWidget).attr('preview', 1);
				$(bgInputImgEleWidget).attr('preview', 0);
			} else {
				$(bgImgEleWidget).attr('preview', 0);
				$(bgInputImgEleWidget).attr('preview', 1);
			}
			
		} else {
			ynps2.notPreviewElements([bgColorEleWidget, bgImgEleWidget]);
			$(bgNoneEleWidget).attr('preview', 1);
		}
	});

	var valueNoneBg = ynps2.getRuleValue($(bgNoneEleWidget).attr('rule_id'));
	
	if (valueNoneBg) {
		$("#content-widget input[name=use_color_radio]").filter("[value=nobg]").prop("checked",true);
	} else {
		var valueImgBg = ynps2.getRuleValue($(bgImgEleWidget).attr('rule_id'));
		if (!valueImgBg) {			
			$("#content-widget input[name=use_color_radio]").filter("[value=color]").prop("checked",true).trigger('click');
		} else {
			$("#content-widget input[name=use_color_radio]").filter("[value=image]").prop("checked",true);
		}
	}

	// when the user input his image by entering the URL, then the dropdownlist image will now be used to preview
	$('#content-widget input[name=background_image][type=text]').change(function() {
		if ($('#content-widget input[name=use_color_radio]:checked').val() == 'image') {
			if ($(this).val() != '') {
				$(bgImgEleWidget).attr('preview', 0);
				$(bgInputImgEleWidget).attr('preview', 1);
			} else {
				$(bgImgEleWidget).attr('preview', 1);
				$(bgInputImgEleWidget).attr('preview', 0);
			}
		}
	});
</script>