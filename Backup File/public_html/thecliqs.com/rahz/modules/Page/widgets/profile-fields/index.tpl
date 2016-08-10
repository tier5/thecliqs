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

<div class="profile_fields">
	<h4><span><?php echo $this->translate("Page Details"); ?></span></h4>
	<ul>
	<?php if ($this->subject->getTitle()): ?>
		<li>
			<span><?php echo $this->translate("Title"); ?></span><span><?php echo $this->subject->getTitle(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->getDescription()): ?>
		<li>
			<span><?php echo $this->translate("Description"); ?></span><span><?php echo $this->subject->getDescription(false, false, false); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->isAddress()): ?>
		<li>
			<span><?php echo $this->translate("Page Address"); ?></span><span><?php echo $this->subject->getAddress(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->website): ?>
		<li>
			<span><?php echo $this->translate("Website"); ?></span><span><?php echo $this->subject->getWebsite(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->phone): ?>
		<li>
			<span><?php echo $this->translate("Phone"); ?></span><span><?php echo $this->subject->phone; ?></span>
		</li>
	<?php endif; ?>
	</ul>

  <?php echo $this->fieldValueLoop($this->subject, $this->fieldStructure); ?>
</div>

<script type="text/javascript">
	$$(".layout_page_profile_fields .tip").destroy();
</script>
