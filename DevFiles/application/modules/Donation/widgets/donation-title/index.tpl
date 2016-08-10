<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adik
 * Date: 25.07.12
 * Time: 15:14
 * To change this template use File | Settings | File Templates.
 */
?>


<h2>
    <?php echo ( '' != trim($this->donation->getTitle())
    ? $this->donation->getTitle()
    : '<em>' . $this->translate('Untitled') . '</em>'); ?>
</h2>