<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<script language="javascript" type="text/javascript">
    function selectAll() {
        var i;
        var multidelete_form = $('multidelete_form');
        var inputs = multidelete_form.elements;
        for (i = 1; i < inputs.length; i++) {
            if (!inputs[i].disabled) {
                if ($(inputs[i]).hasClass('checkbox')) {
                    inputs[i].checked = inputs[0].checked;
                }
            }
        }
    }
    
    function multiDelete() {
        return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected images?")) ?>");
    }
</script>

<h2>
    <?php echo $this->translate('Profile Styler Plugin') ?>
</h2>

<?php if (count($this->navigation)) : ?>
    <div class='tabs'>
        <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render() ?>
    </div>
<?php endif; ?>

<div class="ynprofilestyler-block">
    <a class="buttonlink smoothbox" 
       href="<?php 
           echo $this->url(array(
                'module' => 'ynprofilestyler',
                'controller' => 'manage',
                'action' => 'upload',
                'format' => 'smoothbox'
                    ), 'admin_default')
            ?>">
        <?php echo $this->translate('Add new image') ?>
    </a>
</div>

<?php if (count($this->paginator)) : ?>
    <form id="multidelete_form" method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <table class='admin_table ynprofilestyler_admin_table'>
            <thead>
                <tr>
                    <th>
                        <input onclick='selectAll();' type='checkbox' class='checkbox' />
                    </th>
                    <th><?php echo $this->translate('Images') ?></th>
                    <th><?php echo $this->translate('Created Date') ?></th>
                    <th><?php echo $this->translate('Options') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->paginator as $item) : ?>
                    <tr>
                        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->image_id; ?>' value='<?php echo $item->image_id ?>' /></td>
                        <td>
                            <div class="ynprofilestyler-img" style="background-image:url('<?php echo $item->url ?>')"/>
                        </td>
                        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                        <td>
                            <?php
                        		echo $this->htmlLink($this->url(array(
                        			'module'     => 'ynprofilestyler',
                        			'controller' => 'manage',
                        			'action'     => 'delete-image',
                        			'id'         => $item->image_id), 'admin_default'), 
                        		$this->translate("delete"), array('class' => 'smoothbox'))
                    		?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>	
        <div class="ynprofilestyler-block">
            <?php echo $this->paginationControl($this->paginator, null, null);?>
    	</div>
        <div class='buttons ynprofilestyler-block'>
            <button type='submit' value='delete'>
                <?php echo $this->translate("Delete Selected") ?>
            </button>
        </div>
    </form>
    
<?php else : ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no images.") ?>
        </span>
    </div>
<?php endif; ?>