<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: form.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php if (@$this->closeSmoothbox): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>