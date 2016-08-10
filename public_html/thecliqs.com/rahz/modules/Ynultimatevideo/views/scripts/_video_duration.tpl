<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<span class="ynultimatevideo_length">
    <?php
    if ($this->video->duration >= 3600) {
        $duration = gmdate("H:i:s", $this->video->duration);
    } else {
        $duration = gmdate("i:s", $this->video->duration);
    }
    echo $duration;
    ?>
</span>