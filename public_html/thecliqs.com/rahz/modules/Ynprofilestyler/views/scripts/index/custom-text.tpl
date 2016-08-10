<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<div class="middle area">
	<fieldset>
		<legend><?php echo $this->translate('Link')?></legend>
		<div class="one_row_area">
			<form id="form-text-link">
                <?php echo $this->formLink->getElement('color')->render($this) ?>
            </form>
        </div>
    </fieldset>
</div>

<div class="area">
    <div class="left col1_2cols">
    	<fieldset>
    		<legend><?php echo $this->translate('Username')?></legend>
    		<form id="form-text-username">
            	<div class="font_area">
    			    <?php echo $this->formUsername->getElement('font_family')->render() ?>
    			    <?php echo $this->formUsername->getElement('font_size')->render() ?>
        			<?php echo $this->formUsername->getElement('color')->render() ?>	
    			</div>
    			<div class="font_area">
        			<?php echo $this->formUsername->getElement('font_weight')->render() ?>
        			<?php echo $this->formUsername->getElement('font_style')->render() ?>
        			<?php echo $this->formUsername->getElement('text_decoration')->render() ?>
    			</div>
            </form>
        </fieldset>
    </div>
    <div class="middle">
    	<fieldset>
    		<legend><?php echo $this->translate('Content')?></legend>
        	<form id="form-text-username">
            	<div class="font_area">
    			    <?php echo $this->formContent->getElement('font_family')->render() ?>
    			    <?php echo $this->formContent->getElement('font_size')->render() ?>
        			<?php echo $this->formContent->getElement('color')->render() ?>	
    			</div>
    			<div class="font_area">
        			<?php echo $this->formContent->getElement('font_weight')->render() ?>
        			<?php echo $this->formContent->getElement('font_style')->render() ?>
        			<?php echo $this->formContent->getElement('text_decoration')->render() ?>
    			</div>
            </form>
    	</fieldset>
    </div>
</div>