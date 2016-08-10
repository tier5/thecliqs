<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Are you sure you want to permanently remove this recommendation?") ?></h3>
        <p><?php echo $this->translate("Don't worry, the person you recommended won't be notified that you removed it.") ?></p>
        <br />
        <p>
            <input type="hidden" name="confirm" value="<?php echo $this->id?>"/>
            <button type='submit'><?php echo $this->translate("Confirm") ?></button>
            <?php echo $this->translate(" or ") ?> 
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
            <?php echo $this->translate("cancel") ?></a>
        </p>
    </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
    TB_close();
</script>
<?php endif; ?>