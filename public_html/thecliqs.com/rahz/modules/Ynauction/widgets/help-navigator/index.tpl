<ul class="ynauction_quicklinks_menu">
    <?php foreach($this->items as $id=>$title): ?>
    <li>
        <a class = "<?php echo $id == $this->active?"active":""?>" href="<?php echo $this->url(array('controller'=>'help','action'=>'detail','id'=>$id)) ?>"><?php echo $title ?></a>
    </li>
    <?php endforeach; ?>
</ul>
<style type = "text/css">
.active {
    font-weight: bold;
}
</style>