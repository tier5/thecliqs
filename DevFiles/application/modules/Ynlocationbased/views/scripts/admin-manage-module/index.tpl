<h2><?php echo $this->translate("YouNet Location-based System Plugin") ?></h2>
<?php if (count($this->navigation)): ?>
    <div class='tabs'>
        <?php
        // Render the menu
        echo $this->navigation()->menu()->setContainer($this->navigation)->render()
        ?>
    </div>
<?php endif; ?>
<p>
    <?php echo $this -> translate("This page is for managing supported modules with Location-based System. Admin can enable / disable specific modules. After disabling, all of the widgets belong to that module will not be applied with location-based searching feature.")?>
</p>
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this->url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table' style="position: relative;">
                <thead>
                    <tr>
                        <th>
                             <?php echo $this->translate("Module Name") ?>
                        </th>
                        <th style="width: 15%">
                             <?php echo $this->translate("Enabled") ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item):?>
                        <tr id='module_item_<?php echo $item->module_id ?>'>
                            <td><?php echo $item -> module_title?></td>
                            <td>
                                <div id='ynlocationbased_content_<?php echo $item->module_id; ?>' style ="text-align: center;" >
                                    <input type="checkbox" id='ynlocationbased_<?php echo $item->module_id; ?>' onclick="disableModule(<?php echo $item->module_id; ?>,this)" <?php if($item->enabled==1) echo "checked";?>  />
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </form>
    <br />
    <div>
        <?php echo $this->paginationControl($this->paginator); ?>
    </div>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no module items yet.") ?>
        </span>
    </div>
<?php endif; ?>
<script type="text/javascript">
    function disableModule(module_id,obj){
        var element = document.getElementById('ynlocationbased_content_'+ module_id);
        var status = 0;
        if(obj.checked==true) status = 1;
        else status = 0;
        var content = element.innerHTML;
        element.innerHTML= "<img src='application/modules/Ynlocationbased/externals/images/loading.gif'></img>";
        new Request.JSON({
            'format': 'json',
            'url' : '<?php echo $this->url(array('module' => 'ynlocationbased', 'controller' => 'manage-module', 'action' => 'disable-module'), 'admin_default') ?>',
            'data' : {
                'format' : 'json',
                'module_id' : module_id,
                'status' : status
            },
            'onRequest' : function(){
            },
            'onSuccess' : function(responseJSON, responseText)
            {
                element.innerHTML = content;
                var checkbox = document.getElementById('ynlocationbased_'+module_id);
                if( status == 1) checkbox.checked=true;
                else checkbox.checked=false;
            }
        }).send();

    }
</script>