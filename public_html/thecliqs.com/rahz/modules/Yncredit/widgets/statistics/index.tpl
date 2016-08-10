<div class="yncredit-container">
    <ul>
        <li><span class="icon-credit"></span><?php echo $this -> translate("Current balance:"); ?> <?php echo $this->locale()->toNumber($this -> statistics -> current)?></li>
        <li><span class="icon-credit-up"></span><?php echo $this -> translate("Total earned:"); ?> <?php echo $this->locale()->toNumber($this -> statistics -> earned)?></li>
        <li><span class="icon-credit-down"></span><?php echo $this -> translate("Total spent:"); ?> <?php echo $this->locale()->toNumber($this -> statistics -> spent)?></li>
        <li><span class="icon-credit-up"></span><?php echo $this -> translate("Total bought:"); ?> <?php echo $this->locale()->toNumber($this -> statistics -> bought)?></li>
        <li><span class="icon-credit-down"></span><?php echo $this -> translate("Total sent:"); ?> <?php echo $this->locale()->toNumber($this -> statistics -> sent)?></li>
        <li><span class="icon-credit-up"></span><?php echo $this -> translate("Total received:"); ?> <?php echo $this->locale()->toNumber($this -> statistics -> received)?></li>
    </ul>
</div>