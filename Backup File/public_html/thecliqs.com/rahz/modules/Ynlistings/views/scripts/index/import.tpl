
<?php 
    $this->headLink() ->prependStylesheet('//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css');
    $this->headScript()->appendFile($staticBaseUrl . 'application/modules/Ynlistings/externals/scripts/xls/xls.js');
    $this->headScript()->appendFile($staticBaseUrl . 'application/modules/Ynlistings/externals/scripts/jquery.min.js');
    $this->headScript()->appendFile($staticBaseUrl . 'application/modules/Ynlistings/externals/scripts/jquery.csv-0.71.js');
?>

<script type="text/javascript">
var cancel = false;
var rollback = false;
var import_status = true;
var message = '';
var max_import = <?php echo $this->max_import?>;
var auth = {};
var url = en4.core.baseUrl+'listings/import-one-by-one';
var listings = new Array();
var import_count = 0;
var listings_imported = new Array();
var total = 0;
        
        
function cancel_import(event) {
    event.preventDefault();
    Smoothbox.open($('cancelImport'));
}

function cancel_ok() {
    if (listings.length > 0)
        cancel = true;
    parent.Smoothbox.close();
}
    
function rollback_import(event) {
    event.preventDefault();
    Smoothbox.open($('rollbackImport'));
}

function rollback_ok() {
    if (listings.length > 0)
        rollback = true;
    parent.Smoothbox.close();
}

function import_process() {
    if (import_status && !cancel && !rollback && max_import > 0 && listings.length > 0) {
        new Request.JSON({
            url: url,
            method: 'post',
            data: {
                'listing': JSON.encode(listings[0]),
                'auth': JSON.encode(auth)
            },
            onSuccess: function(json) {
                import_status = json.status;
                if (json.id != null) {
                    listings_imported.push(json.id);
                }
                message = json.message;
                if (json.status) {
                    listings.splice(0, 1);
                }
                if (json.status && json.id != null) {
                    max_import--;
                    import_count++;
                }
                
                var progress = parseInt(import_count / total * 100, 10);
                $$('#progress .progress-bar').setStyle(
                    'width',
                    progress + '%'
                );
                $$('#progress-percent').setStyle('display', 'inline-block').set('text',
                    progress + '%'
                );
            
                import_process();
            }
        }).send();
    }
    else {
        if (!import_status) {
            Smoothbox.close();
            $$('#importFail p')[0].set('text', message);
            Smoothbox.open($('importFail'));
        }
        else if (cancel) {
            Smoothbox.close();
            $$('#importCancel p')[0].set('text', import_count+' <?php echo $this->translate('listing(s) have been imported.')?>');
            Smoothbox.open($('importCancel'));
        }
        else if (rollback) {
            new Request.JSON({
                url: en4.core.baseUrl+'listings/rollback-import',
                method: 'post',
                data: {
                    'listings': JSON.encode(listings_imported)
                },
            }).send();
            Smoothbox.close();
            Smoothbox.open($('importRollback'));
        }
        else if (max_import <= 0) {
            Smoothbox.close();
            $$('#importLimit p')[0].set('text', import_count+' <?php echo $this->translate('listing(s) have been imported.')?>');
            Smoothbox.open($('importLimit'));
        }
        else if (listings.length == 0) {
            Smoothbox.close();
            $$('#importSuccess p')[0].set('text', '<?php echo $this->translate('Total')?> '+import_count+' <?php echo $this->translate('listing(s) have been imported.')?>');
            Smoothbox.open($('importSuccess'));
        }
        
        if (import_count > 0 && !rollback) {
            var filename = $('file_import').get('value').split("\\").pop();
            new Request.JSON({
                url: en4.core.baseUrl+'listings/history-import',
                method: 'post',
                data: {
                    'listings': JSON.encode(listings_imported),
                    'filename': filename
                },
            }).send();
        }
        cancel = false;
        rollback = false;
        import_status = true;
        message = '';
        max_import = <?php echo Engine_Api::_()->getApi('settings', 'core')->getSetting('ynlistings_max_listings', 100)?>;
        auth = {};
        listings = new Array();
        listings_imported = new Array();
        import_count = 0;
        total = 0;
        $$('#progress .progress-bar').setStyle(
            'width',
            0 + '%'
        );
        $$('#progress-percent').setStyle('display', 'inline-block').set('text',
            0 + '%'
        );
        $("submit").show();
        $("btn_import").hide();
        $("progress").hide();
    }
}

function excute_import(error) {
    var file = $('file_import'); 
    var ext = file.get('value').split(".").pop().toLowerCase();
    if (file.files != undefined) {
        var reader = new FileReader();
        reader.onload = function(e) {
            if (ext == 'xls') {
                var data = e.target.result;
                var wb = XLS.read(data, {type: 'binary'});
                var str = XLS.utils.sheet_to_csv(wb.Sheets[wb.SheetNames[0]]);
            }
            
            else {
                str = e.target.result;            // load file values
            }
            var lines = jQuery.csv.toArrays(str);
            
            for(var i=0;i<lines.length;i++) {
                if(lines[i] && lines[i][0] != "#") {
                    listings.push(lines[i]);
                }
            } 
            
            var auth_arr = ['view', 'share', 'comment', 'upload_photos', 'discussion', 'print'];
            <?php if ($this->has_video) : ?>
                auth_arr.push('upload_videos');
            <?php endif; ?>
            for (var i = 0; i < auth_arr.length; i++) {
                auth[auth_arr[i]] = $(auth_arr[i]).get('value');
            }
            total = listings.length;
            $("submit").hide();
            $("btn_import").show();
            $(progress).show();
            import_process();
        }
        if (ext == 'xls' && error) reader.readAsBinaryString(file.files[0]);
        else reader.readAsText(file.files[0]);
    };
}

function import_listings() {
    var file = $('file_import'); 
    var ext = file.get('value').split(".").pop().toLowerCase();
    if(!['csv', 'xls'].contains(ext)) {
        alert('Import file is invalid.');
        return false;
    }
    
    if (ext == 'xls' && file.files != undefined) {
        var fReader = new FileReader();
        fReader.onload = function(e) {
            var data = e.target.result;
            try {
                var wb = XLS.read(data, {type: 'binary'});
                var str = XLS.utils.sheet_to_csv(wb.Sheets[wb.SheetNames[0]]);
                excute_import(false);
            }
            catch (err) {
                error = true;
                excute_import(true);
                fReader.abort();
            }
        }
        fReader.readAsText(file.files[0]);
    }
    else if (ext == 'csv' && file.files != undefined) {
        excute_import(false);
    }
}
</script>

<h3><?php echo $this->translate('Import Listings') ?></h3>
<p><?php echo $this->translate("YNLISTINGS_IMPORT_DESCRIPTION") ?></p>      
<?php
    echo $this->form->render($this);
?>

<a href=""></a>
<div id="btn_import" style="display:none">
    <button onclick="cancel_import(event)"><?php echo $this->translate('Cancel')?></button>
    <button onclick="rollback_import(event)"><?php echo $this->translate('Rollback')?></button>
    <div class="progress-contain">
        <div id="progress" class="progress" style="display: none; margin-top: 10px; width: 400px; float:left">
            <div class="progress-bar progress-bar-success"></div>
        </div>
        <span id="progress-percent" style="margin-top: 10px;"></span>
    </div>
</div>
<script type="text/javascript">
    jQuery.noConflict();
    var new_btn = $('btn_import').clone(true, true);
    $('btn_import').remove();
    $('submit-element').grab(new_btn);
</script>

<div id="pop_up" style="display: none">
    <div id="cancelImport">
        <h3><?php echo $this->translate('Cancel Import Listings')?></h3>
        <p><?php echo $this->translate('Are you sure you want to cancel this process? Some of listings can not be imported successfully.')?></p>
        <button onclick="cancel_ok()"><?php echo $this->translate("Ok") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a class="import_link" href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
    </div>
    <div id="rollbackImport">
        <h3><?php echo $this->translate('Rollback Import Listings')?></h3>
        <p><?php echo $this->translate('Are you sure you want to rollback this process?')?></p>
        <button onclick="rollback_ok()"><?php echo $this->translate("Ok") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a class="import_link" href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
    </div>
    <div id="importFail">
        <h3><?php echo $this->translate('Import Fail')?></h3>
        <p></p>
        <button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Ok") ?></button>
    </div>
    <div id="importCancel">
        <h3><?php echo $this->translate('Import has been canceled')?></h3>
        <p></p>
        <button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Ok") ?></button>
    </div>
    <div id="importRollback">
        <h3><?php echo $this->translate('Import has been rollbacked')?></h3>
        <p><?php echo $this->translate('Import listings process has been rollbacked.')?></p>
        <button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Ok") ?></button>
    </div>
    <div id="importLimit">
        <h3><?php echo $this->translate('Import reaches limit')?></h3>
        <p></p>
        <button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Ok") ?></button>
    </div>
    <div id="importSuccess">
        <h3><?php echo $this->translate('Import successful')?></h3>
        <p></p>
        <button onclick="javascript:parent.Smoothbox.close()"><?php echo $this->translate("Ok") ?></button>
    </div>
</div>
