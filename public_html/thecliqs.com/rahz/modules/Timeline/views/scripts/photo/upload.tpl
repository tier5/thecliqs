<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: upload.tpl 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

?>
<?php if ($this->photo_id): ?>
<script type="text/javascript">
    eval('parent.document.tl_' + '<?php echo $this->type; ?>' + '.load_photo(<?php echo $this->photo_id; ?>)');
    setTimeout(function () {
        parent.Smoothbox.close();
    }, '500');
</script>
<?php else: ?>
<script type="text/javascript">
    var uploadTimelinePhoto = function () {
        document.getElementById('UploadTimelinePhoto').submit();
        $(document.getElementById('Filedata-wrapper')).setStyles({'width':'400px', 'font-weight':'bold'});
        $(document.getElementById('Filedata-wrapper')).set('html', $(document.getElementById('photo-uploader')).get('html'));
    }
</script>

<div style="margin: 20px; text-align: center;">
    <div id="photo-uploader" class="hidden">
        <img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/>
        <?php echo $this->translate('Loading...')?>
    </div>

    <?php echo $this->form->render($this) ?>
</div>
<?php endif; ?>