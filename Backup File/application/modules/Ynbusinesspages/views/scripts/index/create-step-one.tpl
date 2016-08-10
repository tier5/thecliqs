<?php if($this -> notCreateMore):?>
<div class="tip">
	<span><?php echo $this -> translate("Your businesses are reach limit. Please delete some businesses for creating new."); ?></span>
</div>		
<?php return; endif;?>
<div id="create-business-step-one">
    <?php $count = 0; ?>
    <?php foreach ($this->packages as $package) : ?>
        <?php if ($package->isViewable()): ?>
            <?php $count++;?>
            <div class="ynbusinesspages-package-item">
                <div class="package-name"><?php echo $package->title?></div>
                <div class="package-price-group">
                    <div class="package-button">
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynbusinesspages_general',
                            'action' => 'create-step-two',
                            'package_id' => $package->getIdentity()
                        ), $this->translate('Create A Business'), array())?>
                    </div>
                    <div class="package-price">
                        <span class="label"><?php echo $this->translate('Price:')?></span>
                        <?php 
                            $price = ($package->price > 0) ? $this->locale()->toCurrency($package->price, $package->currency) : $this->translate('Free');
                        ?>
                        <span class="price">
                        	<?php
                        	if ($package->valid_amount == 0)
								echo $price.$this->translate(' for never expire');  
							else 
								echo $price.$this->translate(array(' for %s day', ' for %s days', $package->valid_amount), $package->valid_amount);
                        	?>
                    	</span>
                    </div>
                </div>
                
                <div class="package-features-available">
                    <span class="label"><?php echo $this->translate('Features Available:')?></span>
                    <span class="features-available"><?php echo implode(', ', $package->getAvailableFeatures())?></span>
                </div>
                <div class="package-modules-available">
                    <span class="label"><?php echo $this->translate('Modules Available:')?></span>
                    <span class="modules-available"><?php echo implode(', ', $package->getAvailableTitleModules())?></span>
                </div>
                <div class="package-categories-available">
                    <span class="label"><?php echo $this->translate('Categories Available:')?></span>
                    <span class="categories-available"><?php echo implode(', ', $package->getAvailableTitleCategories())?></span>
                </div>	
                <div class="package-modules-description">
                    <?php echo $package->description;?>
                </div>               
            </div> 
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if($count == 0) :?>
    	<div class = 'tip'>
			<span>
	   			<?php echo $this->translate('There are no available packages.'); ?>
	   		</span>
  		</div>
    <?php endif;?>
</div>
