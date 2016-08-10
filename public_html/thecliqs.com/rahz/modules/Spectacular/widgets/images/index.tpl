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

<?php
$baseURL = $this->baseUrl();
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/jquery.min.js');
?>
<script type="text/javascript">
    if (typeof (window.jQuery) != 'undefined') {
        jQuery.noConflict();

<?php if ($this->removePadding): ?>
            jQuery("#global_wrapper").css('padding-top', '0px');
<?php endif; ?>
        setTimeout(function () {
            if (jQuery(".layout_middle").children().length == 1) {
                jQuery("#global_footer").css('margin-top', '165px');
            }
            if (jQuery(".layout_top") && jQuery('.layout_top').find('.layout_middle').children().length == 1)
                jQuery('.layout_top').next().css('margin-top', '720px');
        }, 100);
    }
    var widgetName = 'layout_spectacular_images';
</script> 

<?php
if ((!empty($this->spectacularSignupLoginLink) || !empty($this->spectacularSignupLoginButton)) && !empty($this->isSitemenuExist) && !$this->viewer->getIdentity()):
    echo $this->partial(
            '_addLoginSignupPopupContent.tpl', 'sitemenu', array(
        'isUserLoginPage' => 0,
        'isUserSignupPage' => 0,
        'isPost' => $this->isPost,
        'sitemenuEnableLoginLightbox' => 1, //$this->show_login,
        'sitemenuEnableSignupLightbox' => $this->show_signup_popup
    ));

    Zend_Registry::set('sitemenu_mini_menu_widget', 1);


endif;
?>

<?php if (!empty($this->isSitemenuExist) && (!empty($this->spectacularSignupLoginLink) || !empty($this->spectacularSignupLoginButton))): ?>

    <?php
    $this->headLink()
            ->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/styles/style_sitemenu.css');
    ?>
    <?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Sitemenu/externals/scripts/core.js');
    ?>
<?php endif; ?>

<?php if ($this->spectacularHowItWorks): ?>
    <div style="display: none;" id="how_it_works">
        <?php
        if ($this->spectacularLendingBlockValue):
            echo '<div id="show_help_content" style="width:1200px;margin:0 auto;display:table;">' . $this->spectacularLendingBlockValue . '</div>';
        else:
            ?>
            <div id="show_help_content" style="width:1200px;margin:0 auto;display:table;">
                <span style="font-size:48px;color:#292929;float:left;width:100%;text-align:center;margin:80px 0 0 0;position:absolute;top:0;left:0;right:0;clear:both;">How It Works !</span>
                <div style="transition: opacity 0.8s ease, top 800ms ease;float: left; margin: 150px 0; opacity: 1; padding: 56px 0; text-align: center; width: 33.3%;">
                    <a href="events/">
                        <div style="background-position: center 50%; background-repeat: no-repeat; margin: 0 auto; width: 200px; height: 200px; background-image: url('<?php echo $baseURL ?>/application/modules/Spectacular/externals/images/create-event.png');"></div>
                        <span style="color: #fff; float: left; font-family: sans-serif; font-size: 27px; margin-top: 20px; text-align: center; width: 100%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Create Events</span>
                        <span style="color: #fff; display:inline-block; font-family:  sans-serif; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Create your event, define the tickets and ready for action!</span>
                    </a>
                </div>

                <div style="transition: opacity 0.8s ease, top 800ms ease;float: left; margin: 150px 0; opacity: 1; padding: 56px 0; text-align: center; width: 33.3%;">
                    <a href="events/">
                        <div style="background-position: center 50%; background-repeat: no-repeat; margin: 0 auto; width: 200px; height: 200px; background-image: url('<?php echo $baseURL ?>/application/modules/Spectacular/externals/images/sell-tickets.png');"></div>
                        <span style="color: #fff; float: left; font-family: sans-serif; font-size: 27px; margin-top: 20px; text-align: center; width: 100%;text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Sell Tickets</span>
                        <span style="color: #fff; display:inline-block; font-family: sans-serif; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Sell your event tickets Online.</span>
                    </a>
                </div>

                <div style="transition: opacity 0.8s ease, top 800ms ease;float: left; margin: 150px 0; opacity: 1; padding: 56px 0; text-align: center; width: 33.3%;">
                    <a href="events/">
                        <div style="background-position: center 50%; background-repeat: no-repeat; margin: 0 auto; width: 200px; height: 200px; background-image: url('<?php echo $baseURL ?>/application/modules/Spectacular/externals/images/invitepeople.png');"></div>
                        <span style="color: #fff; float: left; font-family: sans-serif; font-size: 27px; margin-top: 20px; text-align: center; width: 100%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Share Events </span>
                        <span style="color: #fff; display:inline-block; font-family: sans-serif; font-size: 15px; margin-top: 10px; padding: 0 11%; text-align: center; width: 71%; text-shadow: 0 0 3px rgba(0, 0, 0, 0.4);">Promote and Invite people to your events.</span>
                    </a>
                </div>
                <a href="#" style="text-indent:100px;height:20px;width:20px;position:absolute;top:12px;background-image:url('<?php echo $baseURL ?>/application/modules/Spectacular/externals/images/close-icon.png');"></a>
            </div>
        <?php
        endif;
        ?>
    </div>
<?php endif; ?>

<div class="spectacular_images_image_content">
    <div class="spectacular_images_page_container">
        <div class="spectacular_images_top_head">
            <div class="spectacular_images_top_head_left">
                <?php if (!empty($this->showLogo)): ?>
                    <div class="layout_core_menu_logo">
                        <?php
                        $title = $this->coreSettings->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
                        $logo = $this->logo;
                        $route = $this->viewer()->getIdentity() ? array('route' => 'user_general', 'action' => 'home') : array('route' => 'default');
                        echo ($logo) ? $this->htmlLink($route, $this->htmlImage($logo, array('alt' => $title))) : $this->htmlLink($route, $title);
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($this->spectacularBrowseMenus)): ?>
                    <?php
                    echo $this->content()->renderWidget("seaocore.browse-menu-main", array('max' => $this->max));
                    ?>
                <?php endif; ?>
            </div>
            <div class="spectacular_images_top_head_right">
                <?php if (!empty($this->spectacularSignupLoginLink) && !$this->viewer->getIdentity()): ?>
                    <span class="sign_up_login_btn">
                        <?php if (!empty($this->isSitemenuExist) && !empty($this->show_signup_popup)): ?>
                            <a href="<?php echo $this->url(array(), "user_signup", true) ?>" onClick="advancedMenuUserLoginOrSignUp('signup', '', '');
                                    return false;"><?php echo $this->translate("Sign Up"); ?></a>
                           <?php else: ?>
                            <a href="<?php echo $this->url(array(), "user_signup", true) ?>"><?php echo $this->translate("Sign Up"); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($this->isSitemenuExist)): ?>
                            <a href="<?php echo $this->url(array(), "user_login", true) ?>" onClick="advancedMenuUserLoginOrSignUp('login', '', '');
                                    return false;"><?php echo $this->translate("Sign In"); ?></a>
                           <?php else: ?>
                            <a href="<?php echo $this->url(array(), "user_login", true) ?>"><?php echo $this->translate("Sign In"); ?></a>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>

                <?php if ($this->spectacularFirstImprotantLink): ?>
                    <span class="spectacular_images_create_account_btn">
                        <a href="<?php echo $this->spectacularFirstUrl; ?>"><?php echo $this->translate($this->spectacularFirstTitle); ?></a>
                    </span>
                <?php endif; ?>
            </div>     
        </div>
        <div class="spectacular_images_middle_caption">
            <h3><?php echo $this->translate($this->spectacularHtmlTitle); ?></h3>
            <p><?php echo $this->translate($this->spectacularHtmlDescription); ?></p>
            <?php if ($this->spectacularHowItWorks): ?>
                <a href="javascript:void(0);" onclick="showHowItWorks();">
                    <?php echo $this->spectacularLendingBlockTitleValue ? $this->spectacularLendingBlockTitleValue : $this->translate('Get Started'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php if (!empty($this->spectacularSignupLoginButton) && !$this->viewer->getIdentity()): ?>
            <div class="spec_btnsblock">
                <?php if (!empty($this->isSitemenuExist)): ?>
                    <a href="<?php echo $this->url(array(), "user_login", true) ?>" onClick="advancedMenuUserLoginOrSignUp('login', '', '');
                            return false;"><?php echo $this->translate("Sign In"); ?></a>
                   <?php else: ?>
                    <a href="<?php echo $this->url(array(), "user_login", true) ?>"><?php echo $this->translate("Sign In"); ?></a>
                <?php endif; ?>
                <?php if (!empty($this->isSitemenuExist) && !empty($this->show_signup_popup)): ?>
                    <a href="<?php echo $this->url(array(), "user_signup", true) ?>" onClick="advancedMenuUserLoginOrSignUp('signup', '', '');
                            return false;"><?php echo $this->translate("Sign Up"); ?></a>
                   <?php else: ?>
                    <a href="<?php echo $this->url(array(), "user_signup", true) ?>"><?php echo $this->translate("Sign Up"); ?></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if ($this->spectacularSearchBox): ?>
            <div>
                <?php
                echo $this->content()->renderWidget("spectacular.landing-search", array('showLocationSearch' => $this->showLocationSearch, 'showLocationBasedContent' => $this->showLocationBasedContent, 'spectacularSearchBox' => $this->spectacularSearchBox));
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include APPLICATION_PATH . '/application/modules/Spectacular/views/scripts/_imageContent.tpl';
?> 

<script type="text/javascript">
    jQuery(function () {
        jQuery('#show_help_content').find('a').last().on('click', function (e) {
            e.preventDefault();
            jQuery('#how_it_works').css('display', 'none');
        });
        showHowItWorks = function () {
            jQuery("#slide-images").slideDown("slow", function () {
            });
            jQuery("#how_it_works").slideToggle("slow", function () {
            });
        };
    });
</script>
