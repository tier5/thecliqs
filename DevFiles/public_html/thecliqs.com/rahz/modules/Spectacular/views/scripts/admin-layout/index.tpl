<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitehomepagevideo
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-05-15 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<h2>
    <?php echo 'Responsive Spectacular Theme'; ?>
</h2>

<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
</div>

<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>