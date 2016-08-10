<?php if($this -> business -> status == 'expired') :?>
	<h4 style="color:red"><?php echo $this -> translate('Your package is now expired. Please make payment to publish your business again')?></h4>
<?php endif;?>

<div class="ynbusinesspages-package-head-title">
    <span><?php echo $this -> translate('Renewal Notification') ?> : <?php echo $this -> translate('before')?></span>
    <select id='renewal_notification'>
        <option value='month'><?php echo $this -> translate('1 month');?></option>
        <option value='week'><?php echo $this -> translate('1 week');?></option>
        <option value='day'><?php echo $this -> translate('1 day');?></option>
    </select>
</div>

<?php if($this -> currentPackage) :?>
    <h3><?php echo $this -> translate('Current Package');?></h3>
    <?php $currentPackage = $this -> currentPackage;?>
    <div class="ynbusinesspages-package-item">
            <div class="package-name"><?php echo $currentPackage->title?></div>
            <div class="package-price-group">
                <div class="package-button">
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_general',
                        'action' => 'place-order',
                        'packageId' => $currentPackage->getIdentity(),
                        'id' => $this -> business ->getIdentity(),
                    ), $this->translate('Make payment'), array())?>
                </div>
                <div class="package-price">
                    <span class="label"><?php echo $this->translate('Price:')?></span>
                    <?php 
                        $price = ($currentPackage->price > 0) ? $this->locale()->toCurrency($currentPackage->price, $currentPackage->currency) : $this->translate('Free');
                    ?>
                    <span class="price">
                    	<?php
                    	if ($currentPackage->valid_amount == 0)
							echo $price.$this->translate(' for never expire');  
						else 
							echo $price.$this->translate(array(' for %s day', ' for %s days', $currentPackage->valid_amount), $currentPackage->valid_amount);
                    	?>
                	</span>
                </div>      
            </div>      
            <div class="package-features-available">
                <span class="label"><?php echo $this->translate('Features Available:')?></span>
                <span class="features-available"><?php echo implode(', ', $currentPackage->getAvailableFeatures())?></span>
            </div>
            <div class="package-modules-available">
                <span class="label"><?php echo $this->translate('Modules Available:')?></span>
                <span class="modules-available"><?php echo implode(', ', $currentPackage->getAvailableTitleModules())?></span>
            </div>
            <div class="package-categories-available">
                <span class="label"><?php echo $this->translate('Categories Available:')?></span>
                <span class="categories-available"><?php echo implode(', ', $currentPackage->getAvailableTitleCategories())?></span>
            </div>
            <div class="package-modules-description">
                <?php echo $currentPackage->description;?>
            </div>
    </div> 
<?php endif;?>

<h3><?php echo $this -> translate('Available packages');?></h3>
<div style="margin-bottom: 15px"><?php echo $this -> translate('YNBUSINESSPAGES_DASHBOARD_PACKAGE_DESC');?></div>
<?php $count = 0; ?>
<?php foreach ($this->packages as $package) : ?>
    <?php $categoryIds = $this->business->getCategoryIds();?>
    <?php if ($package->isViewable() && !array_diff($categoryIds, $package->category_id)): ?>
        <?php $count++;?>
        <div class="ynbusinesspages-package-item">
            <div class="package-name"><?php echo $package->title?></div>
            <div class="package-price-group">
                <?php 
                    if(isset($this -> currentPackage))
                    {
                        $labelBtn = $this -> translate('Change Package');
                    }
                    else
                    {
                        $labelBtn = $this -> translate('Make Payment');
                    }               
                ?>
                <div class="package-button">
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynbusinesspages_dashboard',
                        'action' => 'package-change',
                        'business_id' => $this -> business ->getIdentity(),
                        'packageId' => $package->getIdentity(),
                    ), $labelBtn, array('class' => 'smoothbox'))?>
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

<script type="text/javascript">
  	window.addEvent('domready', function() {
	  	$('renewal_notification').addEvent('change', function() {
		    var url = '<?php echo $this->url(array('action'=>'renewal-notification', 'business_id' => $this->business->getIdentity()), 'ynbusinesspages_dashboard') ?>';
		    new Request.JSON({
		        url: url,
		        method: 'post',
		        data: {
		            'id': '<?php echo $this->business->getIdentity()?>',
		            'time': this.value,
		        },
		        'onSuccess' : function(responseJSON, responseText)
		        {
		          alert(responseJSON.message);
		        }
		    }).send();
	  	}); 	
  	});
</script>
