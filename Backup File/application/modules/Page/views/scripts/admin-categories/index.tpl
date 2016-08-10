<h2><?php echo $this->translate("Page Categories"); ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='page_admin_tabs'>
    <?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
</div>
<?php endif; ?>


<p style="padding-bottom: 10px;">
    <?php echo $this->translate("PAGE_CATEGORIES_TIP"); ?>
</p>

<script>
    window.addEvent('domready', function(){
        var uiSmoothNewSet = function(spec) {
            Smoothbox.open($('new-set-form'));
        }

        $$('.admin_fields_options_addquestion').addEvent('click', uiSmoothNewSet);
    });
</script>

<div id="new-set-form-cont" style="display: none;">
    <?php echo $this->form->render(); ?>
</div>


<a class="buttonlink admin_fields_options_addquestion" href="javascript://" ><?php echo $this->translate("New Category Set"); ?></a>
<br />
<br />
<style type="text/css">
    .set, .cat {
        border: 1px dotted #666;
        margin-bottom: 2px;
        cursor: pointer;
        font-weight: 9;
    }
    .set {
        width: 500px;
        padding: 5px;
        min-height: 25px;
    }
    .cat {
        width: 492px;
        padding: 3px;
    }
</style>


<?php foreach($this->set as $item): ?>
<div style="width: 502px; padding: 5px; background: #E9F4FA;">
    <a id="set-caption-<?php echo $item['id']?>" style="color: #666;" href="javascript://" onclick="$(this).hide(); $('edit-set-<?php echo $item['id']?>').show().focus();"><?php echo $item['caption']?></a>
    <input class="set-caption-edit-input" id="edit-set-<?php echo $item['id']?>" style="display: none;" type="text" onblur="$(this).hide(); $('set-caption-<?php echo $item['id']?>').empty().show().innerHTML = $(this).get('value'); " value="<?php echo $item['caption']?>" />
    <?php if($item['id'] != 1):?>
    <?php $delSetUrl = $this->url(array('controller'=>'categories', 'action'=>'deleteset', 'module'=>'page', 'setId'=>$item['id']));?>
    <a style="float: right; font-weight: bold" href="<?php echo($delSetUrl);?>" onclick="return confirm('<?php echo($this->translate("This category set will be permanently deleted. Continue?"));?>')">x</a>
    <?php endif; ?>
</div>
<ul class="set" id="set-<?php echo $item['id']?>">
    <?php foreach($item['items'] as $cat): ?>
    <li id="cat-<?php echo $cat['cat_id']?>" <?php if($cat['cat_id'] == 1) echo('style="display: none;"') ?> class="cat"><?php echo $cat['category']?></li>
    <?php endforeach; ?>
</ul>
<br />
<?php endforeach;?>

<script>
    window.addEvent('domready', function() {
        $$('.set-caption-edit-input').addEvent('blur', function(e){
            var setId = $(this).get('id').split('edit-set-')[1],
                    caption = $(this).get('value');
            var url = '<?php echo($this->url(array('controller'=>'categories', 'action'=>'renameset', 'module'=>'page')));?>';
            new Request.HTML({'url': url, 'data': {'setId':setId, 'caption': caption}}).post();
        });

        var from, to;
        s = new Sortables('.set',
                {
                    constrain: false,
                    clone: true,
                    onStart: function(element, clone) {
                        $(element).set('styles', {'border': '2px dotted #5F93B4'});
                        from = $(element).getParent();
                    },
                    onComplete: function(element, clone) {
                        $(element).set('styles', {'border': '1px dotted #5F93B4'});
                        var data = {};
                        for(i=0; i<this.lists.length; i++) {
                            data[$(this.lists[i]).get('id').split('-')[1]] = s.serialize(i);
                        }
                        to = $(element).getParent();
                        new Request.HTML({
                            url:'<?php echo($this->url(array('controller'=>'categories', 'action'=>'reorder', 'module'=>'page')));?>',
                            data : {sets: data, isWithinSet: from.get('id') == to.get('id'), cat_id: $(element).get('id').split('-')[1], new_set_id: to.get('id').split('-')[1]},
                            evalScripts: true
                        }).post();
                    }
                });
    });
</script>
