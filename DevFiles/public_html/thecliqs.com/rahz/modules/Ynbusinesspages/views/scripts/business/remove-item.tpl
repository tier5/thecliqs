<?php if (false === $this->status) : ?>
    <h3><?php echo $this->translate('Cannot Delete Item')?></h3>
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
<?php endif;?>    