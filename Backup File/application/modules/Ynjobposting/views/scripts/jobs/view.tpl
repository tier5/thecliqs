<script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<style>
	#map_canvas{
		width: 100%;
		height: 300px;
		margin-top: 20px;
	}
</style>
<?php $job = $this->job; ?>
<?php $company = $job->getCompany();?>

<h2 class="ynjobposting-main-title"><?php echo $job->title;?></h2>
<?php if ($job->working_place):?>
	<div><i class="fa fa-map-marker"></i> <?php echo $job->working_place;?></div>
<?php endif;?>

<div class="ynjobposting-detail-title">
	<span><i class="fa fa-exclamation-circle"></i> <?php echo $this->translate("Job Description");?></span>
</div>
<div class="ynjobposting-description"><?php echo $job->description;?></div>

<div class="ynjobposting-detail-title">
	<span><i class="fa fa-briefcase"></i> <?php echo $this->translate("Desired Skills & Experience");?></span>
</div>
<div class="ynjobposting-description"><?php echo $job->skill_experience;?></div>

<?php $info = $job -> getInfo();?>
<?php if (count($info)):?>
	<div class="ynjobposting-detail-title">
		<span><i class="fa fa-briefcase"></i> <?php echo $this->translate("Additional Information");?></span>
	</div>
	<?php foreach ($info as $i):?>
		<div class="ynjobposting-detail-additional-item">			
			<div><?php echo $i->header?></div>
			<div><?php echo $i->content?></div>
		</div>
	<?php endforeach;?>
<?php endif;?>

<div class="ynjobposting-detail-title">
	<span><i class="fa fa-building"></i>  <?php echo $this->translate("Employer");?></span>
</div>

<?php if (!is_null($company)):?>
	<div style="font-weight: bold; font-size: 1.1em; margin-bottom: 5px;"><a href="<?php echo $company->getHref();?>"><?php echo $company->name;?></a></div>

	<?php if ($company->getWebsite()):?>
		<div><a href="<?php echo $company->getWebsite();?>" target="_blank"><?php echo $company->getWebsite();?></a></div>
	<?php endif;?>
	<?php if ($company->location):?>
		<div><?php echo $company->location;?></div>
		<?php if ($company->latitude && $company->longitude):?>
		<div id="map_canvas"></div>
		<script>
		function initialize() {
			var map_canvas = document.getElementById('map_canvas');
			var companyLatlng = new google.maps.LatLng(<?php echo $company->latitude;?>, <?php echo $company->longitude;?>);
			var map_options = {
				center: companyLatlng,
				zoom:8,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(map_canvas, map_options)
			var marker = new google.maps.Marker({
			      position: companyLatlng,
			      map: map,
			      title: '<?php echo $company->location;?>'
			});
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		</script>
		<?php endif;?>
	<?php endif;?>
	
<?php endif;?>

