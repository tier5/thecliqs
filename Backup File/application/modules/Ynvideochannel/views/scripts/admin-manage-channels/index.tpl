<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideochannel
 * @author     YouNet Company
 */
?>
<script type="text/javascript">
    function multiDelete() {
        return confirm("<?php echo $this->string()->escapeJavascript($this->translate("Are you sure you want to delete the selected channels?")) ?>");
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

    function setFeaturedVideo(ele) {
        var channelId = $(ele).get('channelid');
        var parentEle = $(ele).getParent();
        var originalHtml = $(parentEle).get('html');
        var request = new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('module' => 'ynvideochannel','controller' => 'manage-channels','action' => 'set-feature'), 'admin_default') ?>',
            'data' : {
                'channel_id' : channelId
            },
            'onRequest' : function(){
                $(parentEle).set('html', "<img src='application/modules/Ynvideochannel/externals/images/loading.gif'></img>");
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

    function setOfDayChannel(ele) {
        var channelId = $(ele).get('channelid');
        var parentEle = $(ele).getParent();
        var originalHtml = $(parentEle).get('html');
        var request = new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('module' => 'ynvideochannel','controller' => 'manage-channels','action' => 'set-of-day'), 'admin_default') ?>',
            'data' : {
                'channel_id' : channelId
            },
            'onRequest' : function(){
                $(parentEle).set('html', "<img src='application/modules/Ynvideochannel/externals/images/loading.gif'></img>");
            },
            'onSuccess' : function(responseJSON, responseText) {
                if (responseJSON.status == 1) {
                    $(parentEle).set('html', originalHtml);

                    $$('.channel_of_day').each(function(el, index){
                        el.checked = false;
                    });
                    var e = $(parentEle).getChildren()[0];
                        e.checked = true;
                } else {
                    alert('<?php echo $this->string()->escapeJavascript($this->translate('There is an error occured. Please try again.'))?>');
                }
            }
        });
        request.send();
    }
    
    function changeOrder(listby, ele){
        var cellEle = $(ele).getParent();
        if ($(cellEle).hasClass('ynvideochannel_order_asc')) {
            $(cellEle).removeClass('ynvideochannel_order_asc');
            $(cellEle).addClass('ynvideochannel_order_desc');
        } else {
            $(cellEle).removeClass('ynvideochannel_order_desc');
            $(cellEle).addClass('ynvideochannel_order_asc');
        }
        var order = "ASC"
        if ($(cellEle).hasClass('ynvideochannel_order_desc')) {
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
    <?php echo $this->translate("Video Channel Plugin") ?>
</h2>

<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
            echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<h3><?php echo $this->translate("Manage Channels") ?></h3>
<p><?php echo $this->translate("YNVIDEOCHANNEL_ADMIN_MANAGECHANNELS_DESCRIPTION") ?></p>
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
                    <th class='admin_table_short' field="channel_id">
                        <a href="javascript:void(0);" onclick="changeOrder('channel_id', this)"><?php echo $this->translate("ID")?></a>
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
                    <th field="is_featured">
                        <a href="javascript:void(0);" onclick="changeOrder('is_featured', this)">
                            <?php echo $this->translate("Featured") ?>
                        </a>
                    </th>
                    <th field="of_day">
                        <a href="javascript:void(0);" onclick="changeOrder('of_day', this)">
                            <?php echo $this->translate("Channel Of The Day") ?>
                        </a>
                    </th>
                    <th field="video_count">
                        <a href="javascript:void(0);" onclick="changeOrder('video_count', this)">
                            <?php echo $this->translate("Videos") ?>
                        </a>
                    </th>
                    <th field="subscriber_count">
                        <a href="javascript:void(0);" onclick="changeOrder('subscriber_count', this)">
                            <?php echo $this->translate("Subscribers") ?>
                        </a>
                    </th>
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
                        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value='<?php echo $item->getIdentity() ?>' /></td>
                        <td><?php echo $item->getIdentity() ?></td>
                        <td><?php echo $item->getTitle() ?></td>
                        <td><?php echo $item->getOwner() ?></td>
                        <td class="ynvideochannel-cell-center">
                            <input type="checkbox" <?php echo ($item->is_featured) ? 'checked' : ''; ?>
                            channelid="<?php echo $item->getIdentity() ?>" onclick="setFeaturedVideo(this)"/>
                        </td>
                        <td class="ynvideochannel-cell-center">
                            <input class="channel_of_day" type="radio" <?php echo ($item->of_day) ? 'checked' : ''; ?>
                            channelid="<?php echo $item->getIdentity() ?>" onclick="setOfDayChannel(this)"/>
                        </td>
                        <td><?php echo $this->locale()->toNumber($item->video_count) ?></td>
                        <td><?php echo $this->locale()->toNumber($item->subscriber_count) ?></td>
                        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                        <td>
                            <a target="_blank" href="<?php echo $item -> getHref(); ?>">
                                <?php echo $this->translate("view") ?>
                            </a>
                            |
                            <?php
                            echo $this->htmlLink(
                                array('route' => 'ynvideochannel_channel', 'action' => 'edit', 'channel_id' => $item->getIdentity()), $this->translate("edit"))
                            ?>
                            |
                            <?php
                            echo $this->htmlLink(
                                array('route' => 'admin_default', 'module' => 'ynvideochannel', 'controller' => 'manage-channels', 'action' => 'delete', 'id' => $item->getIdentity()), $this->translate("delete"), array('class' => 'smoothbox'))
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class='buttons'>
            <button type='submit' value='delete'>
                <?php echo $this->translate("Delete Selected") ?>
            </button>
        </div>
    </form>

    <?php
        echo $this->paginationControl($this->paginator, null, null, array(
            'pageAsQuery' => true,
            'query' => $this->params,
        ));
    ?>
<?php else: ?>
    <div class = "tip">
        <span>
            <?php echo $this->translate("There are no channels posted by your members yet.") ?>
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
                    headerCells[i].addClass('ynvideochannel_order_asc');
                } else if (order == 'DESC') {
                    headerCells[i].addClass('ynvideochannel_order_desc');
                }
                break;
            }
        }
    }
</script>