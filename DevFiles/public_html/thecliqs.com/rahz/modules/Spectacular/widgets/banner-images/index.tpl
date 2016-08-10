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
$this->headLink()->appendStylesheet($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/styles/image_rotate.css');
$this->headScript()->appendFile($this->layout()->staticBaseUrl . 'application/modules/Seaocore/externals/scripts/image_rotate.js');
?>

<script type="text/javascript">
    window.addEvent('domready', function () {
        durationOfRotateImage = <?php echo!empty($this->defaultDuration) ? $this->defaultDuration : 500; ?>;
        image_rotate();
    });

    if (($$('.layout_siteusercoverphoto_user_cover_photo').length > 0) || ($$('.layout_sitecontentcoverphoto_content_cover_photo').length > 0) || ($$('.layout_spectacular_banner_images').length > 0)) {
        $('global_content').setStyles({
            'width': '100%',
            'margin-top': '-16px'
        });
    }
</script>

<style type="text/css">
    #slide-images{
        width: <?php echo!empty($this->slideWidth) ? $this->slideWidth . 'px;' : '100%'; ?>;
        height: <?php echo $this->slideHeight . 'px !important'; ?>;
    }
    .slideblok_image img{
        height: <?php echo $this->slideHeight . 'px !important'; ?>;
    }

    .layout_spectacular_banner_images .bannerimage-text {
        height: <?php echo $this->slideHeight . 'px !important'; ?>;
    }
</style>

<div id="slide-images" class="slideblock">
    <?php
    foreach ($this->list as $imagePath):
        if (!is_array($imagePath)):
            $iconSrc = "application/modules/Spectacular/externals/images/" . $imagePath;
        else:
            $iconSrc = Engine_Api::_()->spectacular()->displayPhoto($imagePath['file_id'], 'thumb.icon');
        endif;
        if (!empty($iconSrc)):
            ?>
            <div class="slideblok_image">
                <img src="<?php echo $iconSrc; ?>" />
            </div>
            <?php
        endif;
    endforeach;
    ?>
    <section class="bannerimage-text">
        <div>
            <?php if ($this->spectacularHtmlTitle): ?>
                <h1><?php echo $this->spectacularHtmlTitle; ?></h1>
            <?php endif; ?>
            <?php if ($this->spectacularHtmlDescription): ?>
                <article><?php echo $this->spectacularHtmlDescription; ?></article>
            <?php endif; ?>
        </div>
    </section>
</div>
