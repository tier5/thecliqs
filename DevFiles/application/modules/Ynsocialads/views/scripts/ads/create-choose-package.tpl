<script type="text/javascript">
	function toStepOne(obj) {
		var value = obj.value;
		window.location.href = en4.core.baseUrl + 'socialads/ads/create-step-one/package_id/'+value;
	}
</script>

<p>
	<?php echo $this->translate("YNSOCIALADS_CREATE_AD_SELECT_PACKAGE") ?>
</p>

<div id="chose_package">
<?php if (!empty($this->packages)) : ?>
<?php foreach ($this->packages as $package) :?>
	<?php if(!$package->isViewable() || !$package->show) continue;?>
	<div class="package-item">
		<div class="package-price">
			<span class="price">
				<?php
				if ($package->price == 0){
					echo $this->translate('FREE');
				}
				else {
					echo $this->locale() -> toCurrency($package->price, $package->currency);
				}
				?>
			</span>
			<span class="title"><?php echo $this->translate($package->title)?></span>
		</div>
		<div class="package-content">
			<p>
				<b><?php
					if ($package->price == 0){
						echo $this->translate('YNSOCIALADS_FREE');
					}
					else {
						echo $this->locale() -> toCurrency($package->price, $package->currency);
					}
					?></b>
				<?php echo ' '.$this->translate('YNSOCIALADS_FOR').' '?>
				<b><?php echo ($package->benefit_amount.' '.strtoupper($package->benefit_type).'S')?></b>
			</p>
			<p>
				<b><?php echo $this->translate('YNSOCIALADS_YOU_CAN_ADVERTISE').': '?></b>
				<?php 
					echo implode(', ', $package->getAllModuleNames());
					if (count($package->getAllModuleNames())) echo  ' '.$this->translate('as').' ';
				
					$strTemp = ucwords(implode(', ', $package->allowed_ad_types));
					$arrTemp = explode(', ', $strTemp);
					foreach($arrTemp as &$item)
					{
						$item = $this->translate($item);
					}
					echo implode(' '.$this->translate('YNSOCIALADS_OR').' ', $arrTemp)
				?>
			</p>
			<p>
				<b><?php echo $this->translate('YNSOCIALADS_DESCRIPTION').': '?></b>
				<?php echo $package->description?>
			</p>

			<button value="<?php echo $package->package_id?>" onclick="toStepOne(this)"><?php echo $this->translate('YNSOCIALADS_CREATE_AD')?></button>
		</div>        
	</div>    
<?php endforeach; ?>
<?php else: ?>
	<div class="tip">
		<span>
		<?php echo $this->translate('Don\'t have any available packages.') ?>
		</span>
	</div>
<?php endif; ?>
</div>
<script type="text/javascript">
	$$('.core_main_ynsocialads').getParent().addClass('active');
</script>
