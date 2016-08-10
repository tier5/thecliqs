<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>

<script type="text/javascript">
    function multiDelete() {
        return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected videos?")) ?>");
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

    function killProcess(video_id) {
        (new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('module' => 'ynultimatevideo', 'controller' => 'admin-manage', 'action' => 'kill'), 'default', true) ?>',
            'data' : {
                'format' : 'json',
                'video_id' : video_id
            },
            'onRequest' : function(){
                $$('input[type=radio]').set('disabled', true);
            },
            'onSuccess' : function(responseJSON, responseText)
            {
                window.location.reload();
            }
        })).send();

    }
    
    function setFeaturedVideo(ele) {
        var videoId = $(ele).get('videoId');
        var parentEle = $(ele).getParent();
        var originalHtml = $(parentEle).get('html');
        var request = new Request.JSON({
            'format': 'json',
            'url' : '<?php 
                echo $this->url(array('module' => 'ynultimatevideo','controller' => 'admin-manage','action' => 'set-feature'), 'default')
                    ?>',
            'data' : {
                'format' : 'json',
                'video_id' : videoId
            },
            'onRequest' : function(){
                $(parentEle).set('html', "<img src='application/modules/Ynultimatevideo/externals/images/loading.gif'></img>");
            },
            'onSuccess' : function(responseJSON, responseText) {
                if (responseJSON.status == 1) {
                    $(parentEle).set('html', originalHtml);
                    var e = $(parentEle).getChildren()[0];
                    if (responseJSON.featured > 0) {
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
    
    function changeOrder(listby, ele){
        var cellEle = $(ele).getParent();
        if ($(cellEle).hasClass('ynultimatevideo_order_asc')) {
            $(cellEle).removeClass('ynultimatevideo_order_asc');
            $(cellEle).addClass('ynultimatevideo_order_desc');
        } else {
            $(cellEle).removeClass('ynultimatevideo_order_desc');
            $(cellEle).addClass('ynultimatevideo_order_asc');
        }
        var order = "ASC"
        if ($(cellEle).hasClass('ynultimatevideo_order_desc')) {
            order = "DESC";
        }
        var orderElement  = new Element('input', {type: 'hidden', name:'order', value:order});
        var orderByElement = new Element('input', {type: 'hidden', name:'fieldOrder', value:listby});
        orderElement.inject($('filter_form'));
        orderByElement.inject($('filter_form'));
        $('filter_form').submit();
    }
</script>

<h2>
    <?php echo $this->translate("Ultimate Video Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        //->setUlClass()
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>

<p>
    <?php echo $this->translate("YNULTIMATEVIDEO_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br />
<div class="admin_search">
    <?php echo $this->form->render($this);?>
</div>
<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <table class='admin_table'>
            <thead>
                <tr>
                    <th class='admin_table_short'>
                        <input onclick='selectAll();' type='checkbox' class='checkbox' />
                    </th>
                    <th class='admin_table_short' field="video_id">
                        <a href="javascript:void(0);" onclick="changeOrder('video_id', this)">ID</a>
                    </th>
                    <th field="title">
                        <a href="javascript:void(0);" onclick="changeOrder('title', this)">
                            <?php echo $this->translate("Title") ?>
                        </a>
                    </th>
                    <th field="owner">
                        <a href="javascript:void(0);" onclick="changeOrder('owner', this)">
                            <?php echo $this->translate("Owner") ?>
                        </a>
                    </th>
                    <th field="featured">
                        <a href="javascript:void(0);" onclick="changeOrder('featured', this)">
                            <?php echo $this->translate("Featured") ?>
                        </a>
                    </th>
                    <th field="view_count">
                        <a href="javascript:void(0);" onclick="changeOrder('view_count', this)">
                            <?php echo $this->translate("Views") ?>
                        </a>
                    </th>
                    <th><?php echo $this->translate("Type") ?></th>
                    <th><?php echo $this->translate("State") ?></th>
                    <th field="creation_date">
                        <a href="javascript:void(0);" onclick="changeOrder('creation_date', this)">
                            <?php echo $this->translate("Date") ?>
                        </a>
                    </th>
                    <th><?php echo $this->translate("Options") ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->paginator as $item): ?>
                    <tr>
                        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->video_id; ?>' value='<?php echo $item->video_id ?>' /></td>
                        <td><?php echo $item->video_id ?></td>
                        <td><?php echo $item->title ?></td>
                        <td><?php echo $item->getOwner() ?></td>
                        <td class="ynultimatevideo-cell-center">
                            <input type="checkbox" <?php echo ($item->status != 1) ? 'disabled' : ''; ?><?php echo ($item->featured) ? 'checked' : ''; ?>
                               videoId="<?php echo $item->getIdentity() ?>" onclick="setFeaturedVideo(this)"/>
                        </td>
                        <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
                        <td>
                            <?php
                            $type = Ynultimatevideo_Plugin_Factory::getSupportType($item->type, 'title');
                            if (!$type) {
                                $type = 'Unknown';
                            }
                            echo $this->translate($type);
                            ?>
                        </td>
                        <td>
                            <?php
                            switch ($item->status) {
                                case 0:
                                    $status = $this->translate("queued");
                                    break;
                                case 1:
                                    $status = $this->translate("ready");
                                    break;
                                case 2:
                                    $status = $this->translate("processing");
                                    break;
                                default:
                                    $status = $this->translate("failed");
                            }
                            echo $status;
                            ?>
                            <?php if ($item->status == 2): ?>
                                (<a href="javascript:void(0);" onclick="javascript:killProcess('<?php echo $item->video_id ?>');">
                                    <?php echo $this->translate("end"); ?>
                                </a>)
                            <?php endif; ?>
                        </td>
                        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                        <td>
                            <a href="<?php echo $this->url(array('user_id' => $item->owner_id, 'video_id' => $item->video_id), 'ynultimatevideo_view') ?>">
                                <?php echo $this->translate("view") ?>
                            </a>
                            |
                            <?php
                            echo $this->htmlLink(
                                array('route' => 'ynultimatevideo_general', 'action' => 'edit', 'video_id' => $item->video_id), $this->translate("edit"))
                            ?>
                            |
                            <?php
                            echo $this->htmlLink(
                                array('route' => 'default', 'module' => 'ynultimatevideo', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->video_id), $this->translate("delete"), array('class' => 'smoothbox'))
                            ?>
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

    <br />

    <div>
        <?php 
            echo $this->paginationControl($this->paginator, null, null, array(
                'pageAsQuery' => true,
                'query' => $this->params,
            )); 
        ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no videos posted by your members yet.") ?>
        </span>
    </div>
<?php endif; ?>   
    
<script language="javascript" type="text/javascript">
    var fieldOrder = '<?php echo (!empty($this->params['fieldOrder']))?$this->params['fieldOrder']:'' ?>';
    var order = '<?php echo (!empty($this->params['fieldOrder']))?$this->params['order']:'' ?>';
    if (fieldOrder) {
        var headerCells = $$('.admin_table > thead > tr > th');
        for (var i = 0; i < headerCells.length; i++) {
            if (headerCells[i].get('field') == fieldOrder) {
                if (order == 'ASC') {
                    headerCells[i].addClass('ynultimatevideo_order_asc');
                } else if (order == 'DESC') {
                    headerCells[i].addClass('ynultimatevideo_order_desc');
                }
                break;
            }
        }
    }
</script>