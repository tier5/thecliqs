<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _themeLandingPageSignupButtonColor.php 2015-06-04 00:00:00Z SocialEngineAddOns $
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

        var r = new MooRainbow('myRainbow7', {
            id: 'myDemo7',
            'startColor': hexcolorTonumbercolor("<?php echo $coreSettings->getSetting('spectacular.landingpage.signupbtn', 'rgba(255,95,63,.5)') ?>"),
            'onChange': function (color) {
                $('spectacular_landingpage_signupbtn').value = color.hex;
            }
        });
    });
</script>

<?php
echo '
	<div id="spectacular_landingpage_signupbtn-wrapper" class="form-wrapper">
		<div id="spectacular_landingpage_signupbtn-label" class="form-label">
			<label for="spectacular_landingpage_signupbtn" class="optional">
				' . $this->translate('Landing Page Sign Up Button') . '
			</label>
		</div>
		<div id="spectacular_landingpage_signupbtn-element" class="form-element">
			<p class="description">' . $this->translate('Select the color of the sign up button available on the landing page.  (Click on the rainbow below to choose your color.)<br>[Note:  If you will change the sign up button color from here then transparency in color will not be visible in the button as it is looking on our demo, but if you want to do so, please read our <a  target="_blank" href="admin/spectacular/settings/faq/faq/faq_4">FAQ</a> section.]') . '</p>
			<input name="spectacular_landingpage_signupbtn" id="spectacular_landingpage_signupbtn" value=' . $coreSettings->getSetting('spectacular.landingpage.signupbtn', 'rgba(255,95,63,.5)') . ' type="text">
			<input name="myRainbow7" id="myRainbow7" src="' . $this->layout()->staticBaseUrl . 'application/modules/Spectacular/externals/images/rainbow.png" link="true" type="image">
                            <a style="margin-bottom:12px;" target="_blank" href="application/modules/Spectacular/externals/images/screenshots/sign-up-button.png" class="buttonlink seaocore_icon_view mleft5"></a>
		</div>
	</div>
'
?>

<script type="text/javascript">
//  function showfeatured(option) {
//    if(option == 1) {
//      $('spectacular_landingpage_signupbtn-wrapper').style.display = 'block';
//    }
//    else {
//      $('spectacular_landingpage_signupbtn-wrapper').style.display = 'none';
//    }
//  }
</script>