<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->error): ?>
<ul class="form-errors"><li><ul class="errors"><li><?php echo $this->message; ?></li></ul></li></ul>
<?php return; endif; ?>

<?php echo $this->form->render($this); ?>