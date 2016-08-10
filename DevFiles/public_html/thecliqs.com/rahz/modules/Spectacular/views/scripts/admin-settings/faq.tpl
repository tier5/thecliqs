<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: faq.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
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
include_once APPLICATION_PATH .
        '/application/modules/Spectacular/views/scripts/admin-settings/faq_help.tpl';
?>
