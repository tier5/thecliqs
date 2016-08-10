<script type="text/javascript">
    en4.core.runonce.add(function(){
        new Sortables('comparison-fields', {
            contrain: false,
            clone: true,
            handle: 'li',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                new Request.JSON({
                    url: '<?php echo $this->url(array('controller'=>'comparison','action'=>'sort'), 'admin_default') ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                    }
                }).send();
            }
        });
    });
    
    function showField(obj) {
        var value = (obj.checked) ? 1 : 0;
        var id = obj.get('value');
        var url = en4.core.baseUrl+'admin/ynbusinesspages/comparison/show';
        new Request.JSON({
            url: url,
            method: 'post',
            data: {
                'id': id,
                'value': value
            }
        }).send();
    }
</script>
<h2>
    <?php echo $this->translate('YouNet Business Pages Plugin') ?>
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

<h3><?php echo $this->translate('Manage Comparison') ?></h3>

<p><?php echo $this->translate("YNBUSINESSPAGES_COMPARISON_MANAGE_DESCRIPTION") ?></p>

<div class="add_link">
<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'comparison', 'action' => 'create'),
    $this->translate('Add Header'), 
    array(
        'class' => 'buttonlink smoothbox add_faq',
    )) ?>
</div>

<ul id="comparison-fields">
<?php foreach ($this->comparisonfields as $field):?>
    <li id="comparison-field_<?php echo $field->getIdentity()?>" <?php if($field->type == 'header') echo 'class="header"';?>>
        <?php if ($field->type == 'header') : ?>
        <span class="title"><?php echo $field->title?></span>
        <span class="float-right">
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'comparison', 'action' => 'delete', 'id' => $field->getIdentity()),
                $this->translate('delete'),
                array('class' => 'smoothbox')
            )?>
        </span>
        <span class="float-right">
            <?php echo $this->htmlLink(
                array('route' => 'admin_default', 'module' => 'ynbusinesspages', 'controller' => 'comparison', 'action' => 'edit', 'id' => $field->getIdentity()),
                $this->translate('edit'),
                array('class' => 'smoothbox')
            )?>
        </span>
        <?php else: ?>
        <input type="checkbox" onclick="showField(this)" value="<?php echo $field->getIdentity()?>" id="field_<?php echo $field->getIdentity()?>" <?php if ($field->show) echo 'checked'?>/>
        <label for="field_<?php echo $field->getIdentity()?>"><?php echo $field->title?></label>
        <?php endif; ?>
    </li>
<?php endforeach; ?>
</ul>