<div class="yncredit-container">
    <ul>
    <?php foreach ($this -> balances as $balance) { ?>
        <li class="yncredit-clearfix">
            <div class="yncredit-top-avatar">
                <?php echo $this -> itemPhoto($balance->getOwner(), 'thumb.icon');?>
            </div>
            <div class="yncredit-top-content">
                <?php echo $balance->getOwner()?>
                <div class="yncredit-color">
                    <span class="icon-credit"></span>
                    <span><?php echo $this->locale()->toNumber($balance -> current_credit)?></span>
                </div>
            </div>
        </li>  
    <?php } ?>
    </ul>
</div>