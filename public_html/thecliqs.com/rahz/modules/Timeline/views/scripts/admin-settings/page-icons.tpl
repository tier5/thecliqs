<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 10.10.12
 * Time: 12:47
 * To change this template use File | Settings | File Templates.
 */?>

<h2><?php echo $this->translate("Timeline Plugin") ?></h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>

<div class="description">
  <?php echo $this->translate('TIMELINE_PageIcons_desc');?>
</div>

<div class='clear'>
  <div class='settings'>

    <div class="timeline_icon_list">
      <?php if (count($this->widgets)) :?>
      <?php foreach($this->widgets as $widget) :?>
        <div class="thumb">
          <div class="title" style="text-align: center" >
            <h3>
              <?php echo $widget->title; ?>
              <?php echo $this->translate("Icon"); ?>
            </h3>
          </div>
          <div class="thumb_body">
            <div>
              <?php if (!$widget->photo_id) :?>
              <img style="border-radius: 5px;" src="<?php echo $this->baseUrl()?>/application/modules/Timeline/externals/images/icons/thumbs/default.png" alt="">
              <?php else :?>
              <img style="border-radius: 5px;" src="<?php echo $widget->getPhotoUrl('thumb.icon'); ?>" alt="">
              <?php endif;?>
            </div>

              <span>
                <div class="button">
                  <?php echo $this->htmlLink(
                  $this->url(array(
                    'module' => 'timeline',
                    'controller' => 'settings',
                    'action' => 'edit',
                    'type' => $widget->type,
                    'p' => '1',
                  ), 'admin_default', true),
                  '<button>' .  $this->translate('Edit') . '</button>', array('title' => $this->translate('Edit')));?>
                </div>
                <?php if ($widget->photo_id) :?>
                <div class="button">
                  <?php echo $this->htmlLink(
                  $this->url(array(
                    'module' => 'timeline',
                    'controller' => 'settings',
                    'action' => 'remove-photo',
                    'type' => $widget->type), 'admin_default', true),
                  '<button>' . $this->translate("Remove") . '</button>', array('class' => 'smoothbox', 'title' => $this->translate('TIMELINE_icon_Delete')));?>
                </div>
                <?php endif; ?>

              </span>
          </div>

        </div>
        <?php endforeach;?>

      <?php else:?>
        <?php echo $this->translate('No available plugins for pages'); ?>
      <?php endif;?>

    </div>

  </div>
</div>

<?php endif; ?>

