<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: message.tpl 2012-08-16 16:46 nurmat $
 * @author     Nurmat
 */
?>
<ul class="form-<?php if ($this->result):?>notices<?php else: ?>errors<?php endif;?>">
    <li><?php echo $this->message?></li>
</ul>