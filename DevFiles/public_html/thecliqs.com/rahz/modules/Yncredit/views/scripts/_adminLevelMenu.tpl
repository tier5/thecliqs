<div style="float: left; width: 15%;">
    <ul>
      <li class="yncredit-menu-tab <?php if ($this->menu == 'index'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'yncredit', 'controller'=>'level', 'action'=>'index'), 'admin_default', true),
          $this->translate('General Settings'),
          array('class'=>'', 'style'=>'float: none')
        );?>
      </li>

      <li class="yncredit-menu-tab <?php if ($this->menu == 'credit'): ?>active-menu-tab<?php endif; ?>">
        <?php echo $this->htmlLink(
          $this->url(array('module'=>'yncredit', 'controller'=>'level', 'action'=>'credit'), 'admin_default', true),
          $this->translate('Credit Settings'),
          array('class'=>'', 'style'=>'float: none')
        );?>
      </li>
    </ul>
</div>