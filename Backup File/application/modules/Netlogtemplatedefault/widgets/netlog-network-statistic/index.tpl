<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Netlogtemplate
 * @copyright  Copyright 2010-2011 SocialEnginePro
 * @license    http://www.socialenginepro.com
 * @author     Vadim
 */
?>

<div class="statistics_icon">&nbsp;</div>
<ul>
	<li>
		<?php echo $this->locale()->toNumber($this->member_count) ?>
		<?php echo $this->translate(array('member', 'members', $this->member_count)) ?>
	</li>
	<li>
		<?php echo $this->locale()->toNumber($this->online_count) ?>
		<?php echo $this->translate('online') ?>
	</li>
</ul>