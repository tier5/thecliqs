<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: edit_photo.tpl 2012-08-16 16:44 nurmat $
 * @author     Nurmat
 */

if ($this->photo): ?>

  <li class="file file-success">
    <span class="file-size"></span>
    <a class="file-remove" id="action_remove" href="javascript:Daylogo.removePhoto(<?php echo $this->photo->getIdentity()?>);" title="<?php echo $this->translate('Click to remove this entry.'); ?>">
			<?php echo $this->translate('DAYLOGO_Remove'); ?>
		</a>
    <span class="file-name"><?php echo $this->translate('DAYLOGO_PHOTO'); ?></span>
    <span class="file-info">
      <img src="<?php echo $this->photo->map(); ?>"/>
    </span>
  </li>

<?php endif;