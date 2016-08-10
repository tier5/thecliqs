<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-08-16 16:40 nurmat $
 * @author     Nurmat
 */

$this->headScript()
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Daylogo/externals/scripts/Daylogo.js');

$this->headTranslate(array(
  'DAYLOGO_DELETE_TITLE',
  'DAYLOGO_DELETE_DESCRIPTION',
  'DAYLOGO_ENABLE_TITLE',
  'DAYLOGO_ENABLE_DESCRIPTION',
  'DAYLOGO_DISABLE_TITLE',
  'DAYLOGO_DISABLE_DESCRIPTION'
));
?>

<script type="text/javascript">
  en4.core.runonce.add(function () {
    Daylogo.url.enable= '<?php echo $this->url(array('action' => 'enable'), 'daylogo_manage');?>';
    Daylogo.url.disable= '<?php echo $this->url(array('action' => 'disable'), 'daylogo_manage')?>';
    Daylogo.url.remove= '<?php echo $this->url(array('action' => 'remove'), 'daylogo_manage')?>';
    Daylogo.init();
  });
</script>

<h2>
  <?php echo $this->translate('Daylogo Plugin') ?>
</h2>

<?php if (count($this->navigation)): ?>
<div class='tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<span>
  <?php echo $this->translate('DAYLOGO_VIEWS_SCRIPTS_ADMININDEX_LOGOLISTS_DESCRIPTION');?>
</span>
<?php endif; ?>
<div class="daylogo_add_logo_icon">
<?php echo $this->htmlLink(
  $this->url(array('module' => 'daylogo', 'controller' => 'admin-index', 'action' => 'create'), 'admin_default', true),
  $this->translate('DAYLOGO_Add New Logo'),
  array('class' => 'buttonlink icon_add')
); ?>
</div>

<div class="daylogo_loader hidden" id="daylogo_loader">
  <?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Daylogo/externals/images/loader.gif'); ?>
</div>
<div class="daylogo" id="daylogo">
  <div class="tab_message hidden tab"></div>
</div>
<?php if (count($this->paginator)): ?>
    <div class="daylogo_logos_lists">
<?php
  foreach ($this->paginator as $item) : ?>
    <div
      class="daylogo_logo_list<?php if ($item->enabled == 0) : echo ' daylogo_logo_disabled'; elseif ($item->active == 1) : echo ' daylogo_active_logo'; endif;?>">
      <div class="daylogo_logo_settings">
        <span>
          <?php
          echo $this->htmlLink(
            $item->enabled == 1 ? 'javascript:Daylogo.disable(' . $item->getIdentity() . ');' : 'javascript:Daylogo.enable(' . $item->getIdentity() . ');',
            '',
            array(
              'class' => $item->enabled == 1 ? 'daylogo_icon_enable' : 'daylogo_icon_disable',
              'title' => $item->enabled == 1 ? $this->translate('DAYLOGO_Disable') : $this->translate('DAYLOGO_Enable')
            ));
          ?>
        </span>
        <span>
            <?php echo $this->htmlLink(
          $this->url(array('action' => 'edit', 'logo_id' => $item->getIdentity()), 'daylogo_manage', true),
          '',
          array('class' => 'daylogo_icon_edit', 'title' => $this->translate('DAYLOGO_Edit'))
        );?>
        </span>
        <span>
            <?php echo $this->htmlLink(
          'javascript:Daylogo.remove(' . $item->getIdentity() . ');',
          '',
          array('class' => 'daylogo_icon_delete', 'title' => $this->translate('DAYLOGO_Delete')
          ));?>
        </span>
      </div>

      <div class="daylogo_logo_img">
        <a
          href="<?php echo $this->url(array('action' => 'preview'), 'daylogo_default');?>/?preview_id=<?php echo $item->photo_id?>"
          title="<?php echo $this->translate('DAYLOGO_Preview'); ?>">
          <?php echo $item->getTitle();?>
          <span style="background-image: url(<?php echo $item->getPhotoUrl(); ?>);"></span>
        </a>
      </div>
      <div class="daylogo_logo_start_end_date">
        <div>
          <label>
            Start Date
          </label>
<?php
$oldTz = date_default_timezone_get();
date_default_timezone_set(Engine_Api::_()->user()->getViewer()->timezone);
echo $this->translate('%1$s at %2$s', $this->locale()->toDate($item->start_date, array()), $this->locale()->toTime($item->start_date));
?>
        </div>
        <div>
          <label>
            End Date
          </label>
<?php
echo $this->translate('%1$s at %2$s', $this->locale()->toDate($item->end_date), $this->locale()->toTime($item->end_date));
date_default_timezone_set($oldTz);
?>
        </div>
      </div>
    </div>
<?php
  endforeach;
?>
</div>
<div>
<?php
  echo $this->paginationControl($this->paginator, null, null, array(
  'pageAsQuery' => true,
  'query' => $this->formValues));
?>
</div>
<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate("DAYLOGO_No_Logos") ?>
    </span>
</div>
<?php endif; ?>