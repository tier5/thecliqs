<fieldset class="package_fieldset">
        <legend><?php echo $this->translate($this->package->title)?></legend>
        <p>
            <span class="bold">
            <?php
            if ($this->package->price == 0){
                echo $this->translate('YNSOCIALADS_FREE');
            }
            else {
                echo ($this->package->price.' '.$this->package->currency);
            }
            ?>
            </span>
            <span><?php echo ' '.$this->translate('YNSOCIALADS_FOR').' '?></span>
            <span class="bold">
            <?php echo ($this->package->benefit_amount.' '.strtoupper($this->package->benefit_type).'S')?>    
            </span>
        </p>
        <p>
            <span class="bold"><?php echo $this->translate('YNSOCIALADS_YOU_CAN_ADVERTISE').': '?></span>
            <span><?php echo ucwords(implode(', ', $this->package->modules))?></span>
            <span> as </span>
            <span><?php 
            $strTemp = ucwords(implode(', ', $this->package->allowed_ad_types));
            $arrTemp = explode(', ', $strTemp);
            echo implode(' '.$this->translate('YNSOCIALADS_OR').' ', $arrTemp)
            ?></span>
        </p>
        <p>
            <span class="bold"><?php echo $this->translate('YNSOCIALADS_DESCRIPTION').': '?></span>
            <span><?php echo $this->package->description?></span>
        </p>
</fieldset>