<?php
/**
 * SocialEngine
 *
 * @category   Application_Ynmobile
 * @package    Ynmobile
 * @copyright  Copyright 2013-2014 YouNet Company
 * @license    http://socialengine.younetco.com/
 * @version    $Id: index.tpl minhnc $
 * @author     MinhNC
 */
?>
<h2>
  <?php echo $this->translate('Younet Mobile Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    	echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
		<?php echo $this->form->render($this) ?>
	</div>
</div>