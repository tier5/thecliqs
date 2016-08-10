<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: files.tpl  01.11.12 16:19 Ulan T $
 * @author     Ulan T
 */

$this->headTranslate(array(
  'Are you sure you want to delete the selected files?'
));
?>

<script type="text/javascript">
    function multiAction()
    {
        return confirm(en4.core.language.translate("Are you sure you want to delete the selected files?"));
    }

    function selectAll()
    {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                inputs[i].checked = inputs[0].checked;
            }
        }
    }

</script>

<?php if( count($this->navigation) ): ?>
<div class='page_admin_tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render();
  ?>
</div>
<?php endif; ?>

<?php if( count($this->sub_navigation) ): ?>
<div class="admin_home_right">
    <ul class="admin_home_dashboard_links">
        <li style="width:200px">
            <ul >
              <?php foreach($this->sub_navigation as $item):?>
                <li class="<?php echo $item->getClass(); ?> hecore-menu-tab <?php if($item->isActive()): ?>active-menu-tab<?php endif; ?>">
                    <a href="<?php echo $item->getHref() ?>">
                      <?php echo $this->translate($item->getLabel()); ?>
                    </a>
                </li>
              <?php endforeach; ?>
            </ul>
        </li>
    </ul>
</div>
<?php endif; ?>

<div class="admin_home_middle" style="clear: none;">
  <?php if( count($this->paginator) ): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(array('module' => 'page', 'controller' => 'import', 'action' => 'multi-delete'), 'admin_default');?>" onSubmit="return multiAction();">
        <table class='admin_table'>
            <thead>
            <tr>
                <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
                <th class='admin_table_short'>ID</th>
                <th class="center"><?php echo $this->translate("File Name") ?></th>
                <th class="center"><?php echo $this->translate("Status") ?></th>
                <th class="center"><?php echo $this->translate("Date") ?></th>
                <th class="center"><?php echo $this->translate("Pages Imported") ?></th>
                <th class="center"><?php echo $this->translate("Options") ?></th>
            </tr>
            </thead>
            <tbody>
              <?php foreach($this->paginator as $item):	 ?>
            <tr class="">
                <td class="center"><input type='checkbox' name='items[]' class='checkbox' value="<?php echo $item->import_id ?>"/></td>
                <td class="center"><?php echo $item->import_id ?></td>
                <td class="center"><?php echo $item->file_name; ?></td>
                <td class="center"><?php echo $item->status; ?></td>
                <td class="center"><?php echo $item->creation_date ?></td>
                <td class="center"><?php echo $item->import_count?></td>
                <td class="center">
                  <?php
                  //start
                  if( $item->status < 2 ) {
                    echo $this->htmlLink($this->url(array('module' => 'page', 'controller' => 'import', 'action' => 'start', 'id' => $item->import_id)), $this->translate('page_import_start_' . $item->status), array('class' => 'smoothbox')) . ' | ';
                  }

                  echo $this->htmlLink($this->url(array('module' => 'page', 'controller' => 'import', 'action' => 'delete', 'id' => $item->import_id)), $this->translate('delete'));
                  ?>
                </td>
            </tr>
              <?php endforeach; ?>
            </tbody>
        </table>
        <br />

        <div class='buttons'>
            <button type='submit'><?php echo $this->translate("Delete") ?></button>
        </div>
    </form>

  <?php else:?>
    <div class="tip">
    <span>
      <?php echo $this->translate("pages_admin there is no files") ?>
    </span>
    </div>
  <?php endif; ?>
</div>
