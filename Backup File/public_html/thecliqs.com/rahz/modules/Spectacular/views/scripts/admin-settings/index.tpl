<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: index.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
if (!empty($this->isModsSupport)):
    foreach ($this->isModsSupport as $modName) {
        echo "<div class='tip'><span>" . $this->translate("Note: You do not have the latest version of the '%s'. Please upgrade it to the latest version to enable its integration with Responsive Spectacular Theme.", ucfirst($modName)) . "</span></div>";
    }
endif;
?>

<?php
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
$spectacularThemeActivated = true;
$themeInfo = Zend_Registry::get('Themes', null);
if (!empty($themeInfo)):
    foreach ($themeInfo as $key => $value):
        if ($key != "spectacular"):
            $spectacularThemeActivated = false;
        endif;
    endforeach;
endif;

if (($coreSettings->getSetting('spectacular.isActivate', 0)) && empty($spectacularThemeActivated)):
    ?>
    <div class="seaocore_tip">
        <span>
            <?php echo "Please activate the 'Spectacular Theme' from 'Layout' >> 'Theme Editor', available in the admin panel of your site." ?>
        </span>
    </div>
<?php endif; ?>

<?php if ((!Engine_Api::_()->hasModuleBootstrap('sitehomepagevideo') || !$coreSettings->getSetting('sitehomepagevideo.isActivate', 0)) && $coreSettings->getSetting('spectacular.isActivate')) : ?>
    <script type="text/javascript">
        function dismissmessage(modName) {
            var d = new Date();
            // Expire after 1 Year.
            d.setTime(d.getTime() + (365 * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = modName + "_dismiss" + "=" + 1 + "; " + expires;
            $('dismiss_modules').style.display = 'none';
        }
    </script>

    <?php
    $moduleName = 'sitehomepagevideo';
    if (!isset($_COOKIE[$moduleName . '_dismiss'])):
        ?>
        <div id="dismiss_modules">
            <div class="seaocore-notice">
                <div class="seaocore-notice-icon">
                    <img src="<?php echo $this->layout()->staticBaseUrl ?>application/modules/Seaocore/externals/images/notice.png" alt="Notice" />
                </div>
                <div style="float:right;">
                    <button onclick="dismissmessage('<?php echo $moduleName; ?>');"><?php echo $this->translate('Dismiss'); ?></button>
                </div>
                <div class="seaocore-notice-text ">
                    <?php echo 'Purchase our ‘<a href="http://www.socialengineaddons.com/socialengine-home-page-background-videos-plugin" target="_blank">Background Videos Plugin</a>’ to beautify your landing page with background video and various impressive options to configure your site header.'; ?>
                </div>	
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>




<?php
if ($coreSettings->getSetting('spectacular.isActivate', 0)):
    include APPLICATION_PATH . '/application/modules/Spectacular/views/scripts/_theme_message.tpl';
endif;
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