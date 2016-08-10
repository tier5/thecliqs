<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>
<script type="text/javascript">
    var fetchLevelSettings = function(level_id) {
        window.location.href = en4.core.baseUrl + 'admin/ynprofilestyler/manage/level/id/' + level_id;
    }
</script>

<h2>
    <?php echo $this->translate('Profile Styler Plugin') ?>
</h2>

<?php if (count($this->navigation)) : ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class='clear'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>
</div>
