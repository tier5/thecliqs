<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _themeBackgroundColor.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>
<?php
$coreSettings = Engine_Api::_()->getApi('settings', 'core');
?>
<script type="text/javascript">
    function hexcolorTonumbercolor(hexcolor) {
        var hexcolorAlphabets = "0123456789ABCDEF";
        var valueNumber = new Array(3);
        var j = 0;
        if (hexcolor.charAt(0) == "#")
            hexcolor = hexcolor.slice(1);
        hexcolor = hexcolor.toUpperCase();
        for (var i = 0; i < 6; i += 2) {
            valueNumber[j] = (hexcolorAlphabets.indexOf(hexcolor.charAt(i)) * 16) + hexcolorAlphabets.indexOf(hexcolor.charAt(i + 1));
            j++;
        }
        return(valueNumber);
    }

    window.addEvent('domready', function () {

        var r = new MooRainbow('myRainbow2', {
            id: 'myDemo2',
            'startColor': hexcolorTonumbercolor("<?php echo $coreSettings->getSetting('spectacular.theme.background.color', '#ff5f3f') ?>"),
            'onChange': function (color) {
                $('spectacular_theme_background_color').value = color.hex;
            }
        });
    });
</script>

<?php
echo '
	<div id="spectacular_theme_background_color-wrapper" class="form-wrapper">
		<div id="spectacular_theme_background_color-label" class="form-label">
			<label for="spectacular_theme_background_color" class="optional">
				' . $this->translate('Website’s Background Color') . '
			</label>
		</div>
		<div id="spectacular_theme_background_color-element" class="form-element">
			<p class="description">' . $this->translate('Select the background color for your website. (Click on the rainbow below to choose your color.)') . '</p>
			<input name="spectacular_theme_background_color" id="spectacular_theme_background_color" value=' . $coreSettings->getSetting('spectacular.theme.background.color', '#ff5f3f') . ' type="text">
			<input name="myRainbow2" id="myRainbow2" src="' . $this->layout()->staticBaseUrl . 'application/modules/Spectacular/externals/images/rainbow.png" link="true" type="image">
                            <a style="margin-bottom:12px;" target="_blank" href="application/modules/Spectacular/externals/images/screenshots/background-color.png" class="buttonlink seaocore_icon_view mleft5"></a>
		</div>
	</div>
'
?>