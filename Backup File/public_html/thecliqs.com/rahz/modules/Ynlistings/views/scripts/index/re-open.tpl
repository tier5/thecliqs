<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Locale.en-US.DatePicker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Picker.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Picker.Attach.js" type="text/javascript"></script> 
<script src="<?php $this->baseURL()?>application/modules/Ynlistings/externals/scripts/picker/Picker.Date.js" type="text/javascript"></script> 
<link href="<?php $this->baseURL()?>application/modules/Ynlistings/externals/styles/picker/datepicker_dashboard.css" rel="stylesheet">
<script type="text/javascript">
    window.addEvent('load', function() {
        new Picker.Date($$('.date_picker'), { 
            positionOffset: {x: 5, y: 0}, 
            pickerClass: 'datepicker_dashboard', 
            useFadeInOut: !Browser.ie,
            timePicker: true,
            format: 'db'
        });
    });
</script>
<?php if (!$this->status) {
        $this->form->error->setValue($this->error);
    }
    else {
        $this->form->removeElement('error');
    } 
?>
<?php echo $this->form->render($this) ?>