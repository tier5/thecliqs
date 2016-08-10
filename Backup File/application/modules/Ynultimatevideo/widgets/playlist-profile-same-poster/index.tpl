<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<div id="ynultimatevideo-playlist-same-poster">
    <?php echo $this->partial('_playlist-listing-side.tpl', 'ynultimatevideo', array('paginator' => $this->paginator, 'formValues' => $this->formValues, 'paging' => true));?>
</div>
