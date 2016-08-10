<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
    function multiDelete() {
        return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected themes?")) ?>");
    }

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
    
    function changeOrder(listby, ele){
        var cellEle = $(ele).getParent();
        if ($(cellEle).hasClass('ynprofilestyler_order_asc')) {
            $(cellEle).removeClass('ynprofilestyler_order_asc');
            $(cellEle).addClass('ynprofilestyler_order_desc');
        } else {
            $(cellEle).removeClass('ynprofilestyler_order_desc');
            $(cellEle).addClass('ynprofilestyler_order_asc');
        }
        var order = "ASC"
        if ($(cellEle).hasClass('ynprofilestyler_order_desc')) {
            order = "DESC";
        }
        var orderElement  = new Element('input', {type: 'hidden', name:'order', value:order});
        var orderByElement = new Element('input', {type: 'hidden', name:'fieldOrder', value:listby});
        orderElement.inject($('filter_form'));
        orderByElement.inject($('filter_form'));
        $('search_form').submit();
    }

    function setActive(ele) {
        var layoutId = $(ele).get('layoutId');
        var parentEle = $(ele).getParent();
        var originalHtml = $(parentEle).get('html');
        var request = new Request.JSON({
            'format': 'json',
            'url' : '<?php 
                echo $this->url(array('module' => 'ynprofilestyler','controller' => 'manage','action' => 'set-active'), 'admin_default')
                    ?>',
            'data' : {
                'format' : 'json',
                'layout_id' : layoutId
            },
            'onRequest' : function(){
                $(parentEle).set('html', "<img src='application/modules/Ynprofilestyler/externals/images/loading.gif'></img>");
            },
            'onSuccess' : function(responseJSON, responseText) {
                if (responseJSON.status == 1) {
                    $(parentEle).set('html', originalHtml);
                    var e = $(parentEle).getChildren()[0];
                    if (responseJSON.is_active) {
                        e.checked = true;
                    } else {
                        e.checked = false;
                    }
                } else {
                    alert('<?php echo $this->string()->escapeJavascript($this->translate('There is an error occured. Please try again.'))?>');
                }
            }
        });
        request.send();
    }
</script>
<h2>
    <?php echo $this->translate('Profile Styler Plugin') ?>
</h2>

<div class='tabs'>
    <?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?>
</div>

<div class="ynprofilestyler-block">
    <a class="buttonlink" 
       href="<?php echo $this->viewer->getHref(array('adminEditing' => 1))?>">
        <?php echo $this->translate('Add new theme') ?>
    </a>
</div>

<div class="admin_search">
    <div class="clear">
        <div class="search">
            <?php echo $this->form->render($this); ?>
        </div>    
    </div>
</div>
<br />
<?php if (count($this->paginator)) : ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <table class='admin_table ynprofilestyler_admin_table'>
            <thead>
                <tr>
                    <th class='admin_table_short'>
                        <input onclick='selectAll();' type='checkbox' class='checkbox' />
                    </th>
                    <th class='admin_table_short' field="title">
                        <a href="javascript:void(0);" onclick="changeOrder('title', this)">
                            <?php echo $this->translate('Theme Name') ?>
                        </a>
                    </th>
                    <th field="creation_date">
                        <a href="javascript:void(0);" onclick="changeOrder('creation_date', this)">
                            <?php echo $this->translate('Created Date') ?>
                        </a>
                    </th>
                    <th field="is_active" class="ynprofilestyler-cell-center">
                        <a href="javascript:void(0);" onclick="changeOrder('is_active', this)">
                            <?php echo $this->translate("Active") ?>
                        </a>
                    </th>
                    <th>
                        <?php echo $this->translate("Options") ?>                            
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->paginator as $item) : ?>
                    <tr>
                        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value='<?php echo $item->getIdentity() ?>' /></td>
                        <td><?php echo $item->title ?></td>
                        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                        <td class="ynprofilestyler-cell-center">                                
                            <input type="checkbox" <?php echo ($item->is_active) ? 'checked' : '' ?> 
                                   layoutId="<?php echo $item->getIdentity() ?>" onclick="setActive(this)"/>                                
                        </td>
                        <td>
                            <?php
		echo $this->htmlLink($this->url(array(
			'module'     => 'ynprofilestyler',
			'controller' => 'manage',
			'action'     => 'edit-layout',
			'id'         => $item->getIdentity()), 'admin_default'), $this->translate("edit"), array('class' => 'smoothbox'))?>
                            |
                            <?php
		echo $this->htmlLink($this->url(array(
			'module'     => 'ynprofilestyler',
			'controller' => 'manage',
			'action'     => 'delete-layout',
			'id'         => $item->getIdentity()), 'admin_default'), $this->translate("delete"), array('class' => 'smoothbox'))?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br />
        <div class='buttons'>
            <button type='submit' value='delete'>
                <?php echo $this->translate("Delete Selected") ?>
            </button>
        </div>
    </form>
    
    <div>
        <?php
			echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query'       => $this->params,
			));
		?>
    </div>
<?php else : ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no themes.") ?>
        </span>
    </div>
<?php endif; ?>