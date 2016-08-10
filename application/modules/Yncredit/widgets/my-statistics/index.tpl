<div class="yncredit-container">
    <ul class="yncredit-clearfix">
        <li><span class="icon-credit"></span><?php echo $this -> translate("Current balance:"); ?> <?php echo $this->locale()->toNumber($this -> balance -> current_credit)?></li>
        <li><span class="icon-credit-up"></span><?php echo $this -> translate("Total earned:"); ?> <?php echo $this->locale()->toNumber($this -> balance -> earned_credit)?></li>
        <li><span class="icon-credit-down"></span><?php echo $this -> translate("Total spent:"); ?> <?php echo $this->locale()->toNumber($this -> balance -> spent_credit)?></li>
        <li><span class="icon-credit-up"></span><?php echo $this -> translate("Total bought:"); ?> <?php echo $this->locale()->toNumber($this -> balance -> bought_credit)?></li>
        <li><span class="icon-credit-down"></span><?php echo $this -> translate("Total sent:"); ?> <?php echo $this->locale()->toNumber($this -> balance -> sent_credit)?></li>
        <li><span class="icon-credit-up"></span><?php echo $this -> translate("Total received:"); ?> <?php echo $this->locale()->toNumber($this -> balance -> received_credit)?></li>
    </ul>
</div>