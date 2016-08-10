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

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<p>
    <?php echo $this->translate("This page contains utilities to help configure and troubleshoot the video plugin.") ?>
</p>
<br/>

<div class="settings">
    <form onsubmit="return false;">
        <h2><?php echo $this->translate("Ffmpeg Version") ?></h2>
        <?php echo $this->translate("This will display the current installed version of ffmpeg.") ?>
        <br/>
        <textarea style="width: 600px;"><?php echo $this->version; ?></textarea>
    </form>
</div>
<br/>
<br/>

<div class="settings">
    <form onsubmit="return false;">
        <h2><?php echo $this->translate("Supported Video Formats") ?></h2>
        <?php echo $this->translate('This will run and show the output of "ffmpeg -formats". Please see this page for more info.') ?>
        <br/>
        <textarea style="width: 600px;"><?php echo $this->format; ?></textarea>
    </form>
</div>
<br/>
<br/>