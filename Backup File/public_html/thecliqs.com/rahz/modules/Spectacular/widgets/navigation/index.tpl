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

<div id="spectacular_navigation_content">
    <div class="headline" style="display: none;"></div>

</div>

<script type="text/javascript">

    if ($$('.layout_spectacular_navigation div').getChildren().length > 2) {
        setTimeout(function () {

            if ($('global_header') && $('global_header').getElement('.layout_sitemenu_menu_main')) {
                if ($('global_wrapper')) {
                    $('global_wrapper').setStyle('padding-top', '166px');
                }
            } else {
                if ($('global_wrapper')) {
                    $('global_wrapper').setStyle('padding-top', '110px');
                }
            }


        }, 1);
    }

    if (<?php echo $this->padding_top; ?>) {
        if ($$('.layout_spectacular_navigation div').getChildren().length == 2) {
            setTimeout(function () {
                if ($('global_wrapper')) {
                    $('global_wrapper').setStyle('padding-top', '55px');
                }
            }, 1);
        }
    } else {
        setTimeout(function () {
            if ($('global_header') && $('global_header').getElement('.layout_sitemenu_menu_main')) {
                $('global_wrapper').setStyle('padding-top', '125px');
            }
        }, 1);
    }

    //en4.core.runonce.add(function () {
    setTimeout(function () {
        if ($('global_wrapper').getElementById('global_content').getElement('.headline')) {
            if ($('global_header') && $('global_header').getElement('.layout_sitemenu_menu_main')) {
                if ($('global_wrapper')) {
                    $('global_wrapper').setStyle('padding-top', '166px');
                }
            } else {
                if ($('global_wrapper')) {
                    $('global_wrapper').setStyle('padding-top', '110px');
                }
            }
            $('spectacular_navigation_content').getElement('.headline').setStyle('display', 'block');
            $('spectacular_navigation_content').getElement('.headline').innerHTML = $('global_wrapper').getElementById('global_content').getElement('.headline').innerHTML;
            //$('global_wrapper').getElementById('global_content').getElement('.headline').innerHTML = '';
            $('global_wrapper').getElementById('global_content').getElement('.headline').setStyle('display', 'none');

            if ($('global_wrapper').getElementById('global_content').getElement('.layout_top') && $('global_wrapper').getElementById('global_content').getElement('.layout_top').getElement('.layout_middle') && $('global_wrapper').getElementById('global_content').getElement('.layout_top').getElement('.layout_middle').getChildren() && $('global_wrapper').getElementById('global_content').getElement('.layout_top').getElement('.layout_middle').getChildren()[0]) {
                $('global_wrapper').getElementById('global_content').getElement('.layout_top').getElement('.layout_middle').getChildren()[0].setStyle('display', 'none');
            }

            if ($('global_wrapper').getElementById('global_content').getElement('.layout_sitemember_navigation_sitemember'))
                $('global_wrapper').getElementById('global_content').getElement('.layout_sitemember_navigation_sitemember').setStyle('display', 'none');

        }
    }, 1);
    //});
</script>