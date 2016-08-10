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

<h2><?php echo $this->translate('sep_mobile_version_title')?></h2>

<div class="mobile-version-descr">
	<?php echo $this->translate('sep_mobile_version_descr')?><br /><br />
	<?php echo $this->htmllink($this->mobile_link['uri'], '<button tabindex="5" type="submit" id="submit" name="submit">Go to Mobile</button>', array('class'=>'lnkGotoMobile'))?>
</div>