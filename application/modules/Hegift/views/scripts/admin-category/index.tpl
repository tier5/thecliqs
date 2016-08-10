<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: index.tpl  03.02.12 16:16 TeaJay $
 * @author     Taalay
 */
?>

<script type="text/javascript">
  var rename = function($cat_id)
  {
    var title = $('category-'+$cat_id).value;
    var url = '<?php echo $this->url(array('module'=>'hegift','controller'=>'category','action'=>'rename'),'admin_default',true)?>';
    new Request.JSON({
      url : url,
      data : {
        format: 'json',
        category_id: $cat_id,
        title: title
      },
      onComplete: function(data) {
        if (data.result) {
          he_show_message(data.message);
        } else {
          he_show_message(data.message, 'error');
        }
      }
    }).send();
  }
</script>

<h2>
  <?php echo $this->translate('Virtual Gifts Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("HEGIFT_VIEWS_SCRIPTS_ADMINCATEGORY_INDEX_DESCRIPTION") ?>
</p>
<br />

<?php echo $this->form->render($this) ?>

<div style="clear: both;"></div>
<br />

<div class='admin_results'>
  <div>
    <?php $count = $this->paginator->getTotalItemCount() ?>
    <?php echo $this->translate(array("HEGIFT_%s category found", "HEGIFT_%s categories found", $this->locale()->toNumber($count)),
        $this->locale()->toNumber($count)) ?>
  </div>
  <div>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
      'pageAsQuery' => true,
    )); ?>
  </div>
</div>
<br />

<div class="admin_table_form">
  <form method="post">
    <table class='admin_table'>
      <thead>
        <tr>
          <th><?php echo $this->translate("HEGIFT_Category Name") ?></th>
          <th class='admin_table_options'><?php echo $this->translate("Options") ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if( count($this->paginator) ): ?>
          <?php foreach( $this->paginator as $category ):?>
            <?php if ($category->category_id == 1) : ?>
              <tr>
                <td><input type="text" value="<?php echo $category->title ?>" id="category-<?php echo $category->category_id?>" size="15" onblur="rename(<?php echo $category->category_id?>)"></td>
              </tr>
            <?php else : ?>
              <tr>
                <td><input type="text" value="<?php echo $category->title ?>" id="category-<?php echo $category->category_id?>" size="15" onblur="rename(<?php echo $category->category_id?>)"></td>
                <td class='admin_table_options'>
                  <a href='<?php echo $this->url(array('module' => 'hegift', 'controller' => 'category', 'action' => 'delete', 'category_id' => $category->category_id), 'admin_default', true);?>' class="smoothbox">
                    <?php echo $this->translate("HEGIFT_delete") ?>
                  </a>
                </td>
              </tr>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </form>
</div>