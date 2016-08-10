<h3><?php echo $this->translate('Pay with %1$s', $this->translate($this -> gateway->title)) ?></h3>
<?php
	echo $this->form->render($this);
?>
<style type="text/css">
    .bill_error {
        color:red;
    }  

    .bill_error a{
        color: blue;
    }
</style>