<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  27.04.12 18:28 TeaJay $
 * @author     Taalay
 */
?>

<h2><?php echo $this->translate("Store Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='store_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate('STORE_ADMIN_FAQ_DESCRIPTION'); ?>
</p>

<br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("%s question found", "%s questions found", $count),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true
    )); ?>
  </div>

  <?php echo $this->htmlLink(
 		$this->url(array('module' => 'store', 'controller' => 'faq', 'action' => 'create'), 'admin_default', true),
 		$this->translate('STORE_Create New FAQ.'),
 		array('class'=>'buttonlink store_icon_faq_create', 'style' => 'float: right')
 	); ?>
</div>

<br />

<div class="admin_table_form">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class="admin_table_centered"><?php echo $this->translate("Question") ?></th>
        <th class="admin_table_centered"><?php echo $this->translate("Answer") ?></th>
        <th class='admin_table_centered admin_table_options'><?php echo $this->translate("Options")?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $faq) : ?>
        <tr>
          <td><?php echo $this->string()->truncate(Engine_String::strip_tags($faq->question), 200, '...'); ?></td>
          <td><?php echo $this->string()->truncate(Engine_String::strip_tags($faq->answer), 200, '...'); ?></td>
          <td class='admin_table_options center'>
            <?php echo $this->htmlLink(
              $this->url(
                array(
                  'module' => 'store',
                  'controller' => 'faq',
                  'action' => 'edit',
                  'faq_id' => $faq->faq_id
                ), 'admin_default', true
              ), $this->translate('edit')) ?>
            <span>&nbsp;|&nbsp;</span>
            <?php echo $this->htmlLink(
              $this->url(
                array(
                  'module' => 'store',
                  'controller' => 'faq',
                  'action' => 'delete',
                  'faq_id' => $faq->faq_id
                ), 'admin_default', true
              ), $this->translate('delete'), array('class' => 'smoothbox')) ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php if (!$count) : ?>
  <br />
  <div class="tip">
    <span>
      <?php echo $this->translate('STORE_There are no FAQ.');?>
    </span>
  </div>
<?php endif; ?>