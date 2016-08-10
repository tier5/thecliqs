<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>
<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>
<?php if( @$this->closeSmoothbox ): ?>
    <script type="text/javascript">
        TB_close();
    </script>
<?php endif; ?>
