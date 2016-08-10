<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 11:20
 * To change this template use File | Settings | File Templates.
 */?>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>


<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>