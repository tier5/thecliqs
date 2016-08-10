<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 21.08.12
 * Time: 10:53
 * To change this template use File | Settings | File Templates.
 */?>

<h2><?php echo $this->translate("Donation Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='donation_admin_tabs'>
  <?php
  // Render the menu
  //->setUlClass()
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
        <h3> <?php echo $this->translate("Donation Categories") ?> </h3>
        <p class="description">
          <?php echo $this->translate("DONATION_ADMINSETTINGS_CATEGORIES_DESCRIPTION") ?>
        </p>
        <?php if(count($this->categories)>0):?>

        <table class='admin_table'>
          <thead>
          <tr>
            <th><?php echo $this->translate("Category Name") ?></th>
            <?php //              <th># of Times Used</th>?>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>

          </thead>
          <tbody>
            <?php foreach ($this->categories as $category): ?>
          <tr>
            <td><?php echo $category->title?></td>
            <td>
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'donation', 'controller' => 'admin-category', 'action' => 'edit-category', 'id' =>$category->category_id), $this->translate("edit"), array(
              'class' => 'smoothbox',
            )) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'donation', 'controller' => 'admin-category', 'action' => 'delete-category', 'id' =>$category->category_id), $this->translate("delete"), array(
              'class' => 'smoothbox',
            )) ?>

            </td>
          </tr>

            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else:?>
        <br/>
        <div class="tip">
          <span><?php echo $this->translate("There are currently no categories.") ?></span>
        </div>
        <?php endif;?>
        <br/>
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'donation', 'controller' => 'category', 'action' => 'add-category'), $this->translate('Add New Category'), array(
        'class' => 'smoothbox buttonlink',
        'style' => 'background-image: url(' . $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/admin/new_category.png);')) ?>
      </div>
    </form>
  </div>
</div>
