<script>
    en4.core.runonce.add(function(){
        new Sortables('badge-items', {
            contrain: false,
            clone: true,
            handle: 'tr',
            opacity: 0.5,
            revert: true,
            onComplete: function(){
                new Request.JSON({
                    url: '<?php echo $this->url(array('module'=>'ynresume','controller'=>'badge','action'=>'sort'), 'admin_default', true) ?>',
                    noCache: true,
                    data: {
                        'format': 'json',
                        'order': this.serialize().toString(),
                    }
                }).send();
            }
        });
    });
</script>
<?php 
    $conditions = array(
        'view' => 'View',
        'completeness' => 'Resume Completeness',
        'endorsements' => 'Endorsements',
        'recommendations' => 'Recommendations'
    );
?>
<h2>
    <?php echo $this->translate('YouNet Resume Plugin') ?>
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

<h3><?php echo $this->translate('Badge Management') ?></h3>

<div class="add_link">
<?php echo $this->htmlLink(
    array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'badge', 'action' => 'create'),
    $this->translate('Create a new badge'), 
    array(
        'class' => 'buttonlink add_faq',
    )) ?>
</div>
<?php if(count($this->badges)): ?>
<div class="ynadmin-table">
    <table class='admin_table' style="position: relative">
        <thead>
            <tr>
                <th><?php echo $this->translate("Badge Title") ?></th>
                <th><?php echo $this->translate("Icon") ?></th>
                <th><?php echo $this->translate("Condition") ?></th>
                <th><?php echo $this->translate("Value") ?></th>
                <th><?php echo $this->translate("Options") ?></th>
            </tr>
        </thead>
        <tbody id="badge-items" >
        <?php foreach ($this->badges as $badge): ?>
            <tr id="badge-item_<?php echo $badge->getIdentity()?>">
                <td><?php echo $badge->getTitle() ?></td>
                <td><?php echo $this->itemPhoto($badge) ?></td>
                <td><?php echo $conditions[$badge->condition] ?></td>
                <?php 
                if ($badge->condition == 'completeness') {
                    $value = 'Complete: ';
                    $arr = unserialize($badge->value);
                    $label_arr = array_map(function ($v) {
                        return Engine_Api::_()->ynresume()->getSectionLabel($v);
                    }, $arr);
                    $value = $value.implode(', ', $label_arr);
                }
                else {
                    $value = $badge->value;
                }
                ?>
                <td><?php echo $value; ?></td>
                <td>
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'badge', 'action' => 'edit', 'id' => $badge->getIdentity()),
                    $this->translate('edit')
                )?>
                |
                <?php echo $this->htmlLink(
                    array('route' => 'admin_default', 'module' => 'ynresume', 'controller' => 'badge', 'action' => 'delete', 'id' => $badge->getIdentity()),
                    $this->translate('delete'),
                    array('class' => 'smoothbox')
                )?> 
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<br/>
<?php else: ?>
<div class="tip">
    <span><?php echo $this->translate("There are no Badges.") ?></span>
</div>
<?php endif; ?>