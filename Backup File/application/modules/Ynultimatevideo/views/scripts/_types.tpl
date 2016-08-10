<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<?php
    $videoTypes = Ynultimatevideo_Plugin_Factory::getAllSupportTypes();
    $ffmpeg_enable = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynultimatevideo.ffmpeg.path');
    if (empty($ffmpeg_enable)) unset($videoTypes[Ynultimatevideo_Plugin_Factory::getUploadedType()]);
?>
ynultimatevideo = {};
ynultimatevideo.types = [];

<?php foreach ($videoTypes as $key => $type) : ?>
    var type = {
        'title' : '<?php echo $this->translate($type)?>',
        'value' : <?php echo $key?>
    }
    ynultimatevideo.types.push(type);
<?php endforeach; ?>

