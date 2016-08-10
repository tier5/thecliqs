<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<style>
.ynjobposting-company-profile-info #map_canvas{
	width: 100%;
	height: 300px;
	margin-top: 20px;
}
</style>
<div class="ynjobposting-company-profile-info">
	<ul>
	<?php if ($this->company->location):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-map-marker"></i><?php echo $this->translate("Headquaters");?></span>
			<span><?php echo $this->company->location;?></span>
		</li>
	<?php endif;?>
	<?php if ($this->company->website):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-globe"></i><?php echo $this->translate("Website");?></span>
			<a href="<?php echo $this->company->getWebsite();?>" target="_blank"><?php echo $this->company->getWebsite();?></a>
		</li>
	<?php endif;?>
	<?php if ($this->company->from_employee && $this->company->to_employee):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-building"></i><?php echo $this->translate("Size");?></span>
			<span><?php echo $this->company->from_employee;?></span> - 
			<span><?php echo $this->company->to_employee;?></span>
		</li>
	<?php endif;?>
	<?php if (count($this->industries)):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-folder-open"></i><?php echo $this->translate("Industry");?></span>
			<span><?php echo $this->industryNames;?></span>
		</li>
	<?php endif;?>
	</ul>
	
	<?php if (count($this -> companyInfo)): ?>
	<h3><?php echo $this -> translate('Additional Information'); ?> </h3>
	<div style="margin-bottom: 1em;">
		<?php foreach($this -> companyInfo  as $companyInfo) :?>
		<div class="ynjobposting-company-profile-additional-item">
			<span class="ynjobposting-company-profile-additional-label"><?php echo $companyInfo -> header ;?></span>
			<span class="ynjobposting-description"><?php echo ($companyInfo -> content) ;?></span>
		</div>
		<?php endforeach;?>
	</div>
	<?php endif;?>
	
	<?php $this -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>	
	<?php if($this -> fieldValueLoop($this -> company, $this -> fieldStructure)):?>
		<h3><?php echo $this -> translate('Specification'); ?> </h3>
		<?php echo $this -> fieldValueLoop($this -> company, $this -> fieldStructure); ?>
	<?php endif; ?>

	<h3><?php echo $this -> translate('Contact Information'); ?> </h3>
	<ul>
	<?php if ($this->company->contact_name):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-check"></i><?php echo $this->translate("Name");?></span>
			<span><?php echo $this->company->contact_name;?></span>
		</li>
	<?php endif;?>
	<?php if ($this->company->contact_email):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-envelope"></i><?php echo $this->translate("Email");?></span>
			<a href="mailto:<?php echo $this->company->contact_email;?>"><?php echo $this->company->contact_email;?></a>
		</li>
	<?php endif;?>
	<?php if ($this->company->contact_phone):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-phone"></i><?php echo $this->translate("Phone");?></span>
			<span><strong><?php echo $this->company->contact_phone;?></strong></span>
		</li>
	<?php endif;?>
	<?php if ($this->company->contact_fax):?>
		<li>
			<span class="ynjobposting-company-profile-info-label"><i class="fa fa-fax"></i><?php echo $this->translate("Fax");?></span>
			<span><strong><?php echo $this->company->contact_fax;?></strong></span>
		</li>
	<?php endif;?>
	</ul>

	<?php if ($this->company->location):?>
	<h3><?php echo $this -> translate('Location'); ?> </h3>
		<div><?php echo $this->company->location;?></div>
		<?php if ($this->company->latitude && $this->company->longitude):?>
			<div id="map_canvas"></div>
			<script>
				function initialize() {
					var map_canvas = document.getElementById('map_canvas');
					var companyLatlng = new google.maps.LatLng(<?php echo $this->company->latitude;?>, <?php echo $this->company->longitude;?>);
					var map_options = {
						center: companyLatlng,
						zoom:8,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					var map = new google.maps.Map(map_canvas, map_options)
					var marker = new google.maps.Marker({
					      position: companyLatlng,
					      map: map,
					      title: '<?php echo $this->company->location;?>'
					});
				}
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>
		<?php endif;?>
	<?php endif;?>
</div>
