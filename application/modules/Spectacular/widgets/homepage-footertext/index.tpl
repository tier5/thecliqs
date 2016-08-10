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
?>

<?php if (!$this->viewer()->getIdentity()): ?>
    <h3>
        <?php echo $this->translate("_SPECTACULAR_FOOTER_TITLE"); ?>
    </h3>

    <p class="desc-text">
        <?php echo $this->translate("_SPECTACULAR_FOOTER_DESCRIPTION"); ?>
    </p>

    <div class="signupblock">
        <?php if (!empty($this->isSitemenuExist) && !empty($this->show_signup_popup_footer) && !empty($this->show_signup_popup)): ?>
            <a href="javascript:void(0)" onClick="advancedMenuUserLoginOrSignUp('signup', '', '')"><?php echo $this->translate("Create Your Account"); ?></a>
        <?php else: ?>
            <a href="<?php echo $this->url(array(), "user_signup", true) ?>"><?php echo $this->translate("_SPECTACULAR_FOOTER_BUTTON_TEXT"); ?></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php
if (!empty($this->sitemenuEnable) && empty($this->sitemenu_mini_menu_widget) && !$this->viewer()->getIdentity()):
    echo $this->partial(
            '_addLoginSignupPopupContent.tpl', 'sitemenu', array(
        'isUserLoginPage' => $this->isUserLoginPage,
        'isUserSignupPage' => $this->isUserSignupPage,
        'isPost' => $this->isPost,
        'sitemenuEnableLoginLightbox' => 0,
        'sitemenuEnableSignupLightbox' => $this->show_signup_popup_footer
    ));

    Zend_Registry::set('sitemenu_mini_menu_widget', 1);
endif;
?>