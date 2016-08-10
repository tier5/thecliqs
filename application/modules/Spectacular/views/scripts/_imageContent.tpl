<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: _imageContent.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
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
</script>

<style type="text/css">
    #slide-images{
        width: <?php echo!empty($this->slideWidth) ? $this->slideWidth . 'px;' : '100%'; ?>;
        height: <?php echo $this->slideHeight . 'px;'; ?>;
    }
    .slideblok_image img{
        height: <?php echo $this->slideHeight . 'px;'; ?>;
    }
</style>

<div class="wrapper-image slideblock" id="slide-images">
    <?php
    foreach ($this->list as $imagePath):
        if (!is_array($imagePath)):
            $iconSrc = "application/modules/Spectacular/externals/images/" . $imagePath;
        else:
            $iconSrc = Engine_Api::_()->spectacular()->displayPhoto($imagePath['file_id'], 'thumb.icon');
        endif;
        if (!empty($iconSrc)):
            ?>
            <div class="screen-image slideblok_image">
                <img src="<?php echo $iconSrc; ?>" />
            </div>
            <?php
        endif;
    endforeach;
    ?>
    <div class="slideoverlay"></div>
</div>
