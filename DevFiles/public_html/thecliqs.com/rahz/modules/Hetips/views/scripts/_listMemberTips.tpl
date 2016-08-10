<div class="tabs">
  <ul class="navigation">
      <?php foreach($this->tipsTypes as $item): ?>
        <?php if(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled($item['type'])): ?>
          <li>
            <?php echo $this->htmlLink(array('module' => 'hetips', 'controller' => 'index', 'type' => $item['type']),
                       $this->translate($item['label']),
                       array('class' => 'hetips_admin_main_settings_' .$item['type'])); ?>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
</div>