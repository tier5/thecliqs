<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-08-31 17:53 idris $
 * @author     Idris
 */
?>

<div>
  <?php echo $this->htmlLink( $this->subject()->getHref(), $this->itemPhoto($this->subject(), 'thumb.profile') ); ?>
	<div class="icons">
	<?php if ( $this->subject()->isStore()) : ?>
		<img class="page-icon" src="application/modules/Page/externals/images/store_mini.png" title="<?php echo $this->translate('STORE_Store'); ?>">
	<?php endif; ?>
	<?php echo $this->htmlImage("application/modules/Page/externals/images/featured".$this->subject()->featured.".png",
		$this->translate('PAGE_page_featured'.$this->subject()->featured),
		array(
		 'class'=>'page-icon',
			'title' => $this->translate('PAGE_page_featured'.$this->subject()->featured)
		)); ?>

	<?php echo $this->htmlImage("application/modules/Page/externals/images/sponsored".$this->subject()->sponsored.".png",
		$this->translate('PAGE_page_sponsored'.$this->subject()->sponsored),
		array(
		 'class'=>'page-icon',
			'title' => $this->translate('PAGE_page_sponsored'.$this->subject()->sponsored)
		)); ?>
	</div>
</div>