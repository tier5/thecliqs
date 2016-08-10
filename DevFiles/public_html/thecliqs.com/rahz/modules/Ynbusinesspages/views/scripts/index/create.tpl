<div id="create-business">
    <p class="label"><?php echo $this->translate('Reason for creating new business')?></p>
    <div id="choose-form">
        <input type="radio" id="persional_purpose" name="create-type" checked />
        <label for="persional_purpose"><?php echo $this->translate('For my personal purpose.')?></label>
        <br />
        <input type="radio" id="claim" name="create-type"/>
        <label for="claim"><?php echo $this->translate('For claiming.')?></label>
        <br />
        <button onclick="toNextStep()"><?php echo $this->translate('Continue')?></button>
    </div>
</div>

<script type="text/javascript">
    function toNextStep() {
        if ($('persional_purpose').checked) {
            url = '<?php echo $this->url(array('action' => 'create-step-one'), 'ynbusinesspages_general', true)?>';
        }
        else {
            url = '<?php echo $this->url(array('action' => 'create-for-claiming'), 'ynbusinesspages_general', true)?>';
        }
        window.location = url;
    }
</script>
