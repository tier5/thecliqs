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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Spectacular/externals/styles/mooRainbow.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Spectacular/externals/scripts/mooRainbow.js');
?>
<?php
$spectacularThemeActivated = true;
$themeInfo = Zend_Registry::get('Themes', null);
if (!empty($themeInfo)):
    foreach ($themeInfo as $key => $value):
        if ($key != "spectacular"):
            $spectacularThemeActivated = false;
        endif;
    endforeach;
endif;

if ((Engine_Api::_()->getApi('settings', 'core')->getSetting('spectacular.isActivate', 0)) && empty($spectacularThemeActivated)):
    ?>
    <div class="seaocore_tip">
        <span>
            <?php echo "Please activate the 'Spectacular Theme' from 'Layout' >> 'Theme Editor', available in the admin panel of your site." ?>
        </span>
    </div>
<?php endif; ?>

<h2><?php echo "Responsive Spectacular Theme" ?></h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>
<?php
$this->form->setDescription("Below, you will be able to choose color scheme for your theme by selecting the radio buttons given below. You can also select the 'Custom Colors' option to customize your theme according to your site from the various available options. <br /><br />  [<b>Note:<b> If you are unable to customize your theme color scheme by yourself then you can purchase our service <a href='http://www.socialengineaddons.com/services/socialengineaddons-theme-customization-service' target='_blank'>SocialEngineAddOns Theme Customization Service</a>. Purchasing of this service will allow you to have our seamless support and assistance in customizing color scheme of your theme.]");
$this->form->getDecorator('Description')->setOption('escape', false);
?>
<div class='seaocore_settings_form'>
    <div class='settings'>
        <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">
    function changeThemeCustomization() {
        if ($("theme_customization-3").checked) {
            $("spectacular_theme_color-wrapper").style.display = 'block';
            $("spectacular_theme_button_border_color-wrapper").style.display = 'block';
            $("spectacular_landingpage_signinbtn-wrapper").style.display = 'block';
            $("spectacular_landingpage_signupbtn-wrapper").style.display = 'block';
            $("spectacular_theme_background_color-wrapper").style.display = 'block';
            if ($("spectacular_theme_containers_background_color-wrapper"))
                $("spectacular_theme_containers_background_color-wrapper").style.display = 'block';
            if ($("spectacular_navigation_background_color-wrapper"))
                $("spectacular_navigation_background_color-wrapper").style.display = 'block';
        } else {
            $("spectacular_theme_color-wrapper").style.display = 'none';
            $("spectacular_theme_button_border_color-wrapper").style.display = 'none';
            $("spectacular_landingpage_signinbtn-wrapper").style.display = 'none';
            $("spectacular_landingpage_signupbtn-wrapper").style.display = 'none';
            $("spectacular_theme_background_color-wrapper").style.display = 'none';
            if ($("spectacular_theme_containers_background_color-wrapper"))
                $("spectacular_theme_containers_background_color-wrapper").style.display = 'none';
            if ($("spectacular_navigation_background_color-wrapper"))
                $("spectacular_navigation_background_color-wrapper").style.display = 'none';
        }
    }

    window.addEvent('domready', function () {
        changeThemeCustomization();
    });
</script>
