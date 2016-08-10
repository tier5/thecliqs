<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _editMenu.tpl 2011-09-21 17:53 mirlan $
 * @author     Mirlan
 */
?>
<div class="admin_home_right" style="width:200px">
    <ul class="admin_home_dashboard_links">
        <li style="width:200px">
            <ul >
                <?php foreach($this->container as $item):?>
                <li class="hecore-menu-tab <?php if($item->isActive()): ?>active-menu-tab<?php endif; ?>">
                    <a href="<?php echo $item->getHref() ?>">
                        <?php echo $this->translate($item->getLabel()); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>
</div>