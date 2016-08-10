<?php if (false === $this->status) : ?>
    <h3><?php echo $this->translate('Cannot get Jobs')?></h3>
    <div class="global_form_popup getjobs_err">
        <div class="error">
            <div class="fa fa-exclamation-triangle"></div>
            <div class="err_message"><?php echo $this->error;?></div>
        </div>
        <div class="cancel"><button onclick = "parent.Smoothbox.close();"><?php echo $this->translate('Close'); ?></button></div>
    </div>
<?php else: ?>
    <?php echo $this->form->render($this) ?>
    <script type="text/javascript">
    $('company_id').addEvent('change', function(){
        window.location.href = '<?php echo $this->url(array('controller' => 'job', 'action' => 'import', 'business_id' => $this->business->getIdentity()), 'ynbusinesspages_extended', true)?>/company_id/'+this.get('value');
    });
    </script>
<?php endif;?>    