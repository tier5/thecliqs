<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>
<span class="ynvideochannel_length">
    <?php
    if ($this->video->duration >= 3600) {
        $duration = gmdate("H:i:s", $this->video->duration);
    } else {
        $duration = gmdate("i:s", $this->video->duration);
    }
    echo $duration;
    ?>
</span>