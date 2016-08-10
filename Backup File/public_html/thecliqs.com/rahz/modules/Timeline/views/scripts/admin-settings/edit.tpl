<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 12.10.12
 * Time: 10:43
 * To change this template use File | Settings | File Templates.
 */?>
<?php if (!$this->isPageOn) :?>
<style type="text/css">
  .timeline_admin_main_pageIcons {
    display: none;
  }
</style>
<?php endif; ?>

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
  <?php echo $this->translate('TIMELINE_EditIcons_desc');?>
</div>


<?php endif; ?>

<div class="admin_home_right" style="width: 200px">
  <ul class="admin_home_dashboard_links">
    <li style="width:200px">
      <ul>
        <li class="hecore-menu-tab ">
          <a class="hecore-menu-link icon_back" href="<?php echo $this->url(array('module' => 'timeline', 'controller' => 'settings', 'action' => $this->page ? 'page-icons': 'thumb-icons'), 'admin_default', true);?>"> <?php echo $this->translate("Back to thumbs list");?> </a>
        </li>
      </ul>
    </li>
  </ul>
</div>

<?php echo $this->form->render($this); ?>