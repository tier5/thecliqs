<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<h2><?php echo $this->translate("Ultimate Video Plugin") ?></h2>

<script type="text/javascript">
    var fetchLevelSettings = function(level_id) {
        window.location.href = en4.core.baseUrl + 'admin/ynultimatevideo/settings/level/id/' + level_id;
    }
</script>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<div class='ynultimatevideo_clear'>
    <div class='settings'>
        <?php echo $this->form->render($this) ?>
    </div>

</div>