<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Spectacular
 * @copyright  Copyright 2015-2016 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: place-htaccess-file.tpl 2015-06-04 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */
?>

<form method="post" class="global_form_popup">
    <div>
        <h3><?php echo $this->translate("Create .htaccess File?") ?></h3>
        <p>
            <?php
            echo $this->translate("You are about to create a new file “.htacces file” over here '/application/themes/spectacular/'. Are you sure you want to create this file?");
            ?>		
        </p>
        <br />
        <p>
            <button type='submit'><?php echo $this->translate("Create / Modified File") ?></button>
            <?php echo $this->translate(" or ") ?> 
            <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
                <?php echo $this->translate("cancel") ?></a>
        </p>
    </div>
</form>