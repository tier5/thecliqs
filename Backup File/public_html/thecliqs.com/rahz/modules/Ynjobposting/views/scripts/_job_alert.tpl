<table width="570" cellpadding="0" cellspacing="0" style="font-family: tahoma, arial, verdana, sans-serif; margin: 0 auto; background-color: #fff; font-size: 10px;">
	<tr>
		<td colspan="3" style="font-size: 12px; color: #fff; text-align: center; height: 55px; vertical-align: middle; background-color: #2995c0;">
		<!-- header -->
		<?php echo $this -> translate('Here are jobs you may be interested in from ') . $this -> layout() -> siteinfo['title'] . ""; ?>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="height: 5px; background-image: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Ynjobposting/externals/images/pattern_letter.png'; ?>);"></td>
	</tr>

	<?php foreach($this -> toSendJobIds as $job_id) :?>
		<?php $job = Engine_Api::_() -> getItem('ynjobposting_job', $job_id);
			$company = $job -> getCompany();
		?>
		<tr>
			<td width="70" style="padding: 10px; vertical-align: top;">
				<div style="width: 70px; height: 70px; overflow: hidden;">
					<!-- image -->
					<?php if($company -> getPhotoUrl()) :?>
						<?php echo "<img style='height: 70px; width: 70px;' width='70' height='70' src='" .'http://' . $_SERVER['HTTP_HOST'] . $company -> getPhotoUrl() ."'>". ""; ?>
					<?php else:?>
						<?php echo "<img style='height: 70px; width: 70px;' width='70' height='70' src='" . $this->layout()->staticBaseUrl . 'application/modules/Ynjobposting/externals/images/default_company.png' ."'>". ""; ?>
					<?php endif;?>
				</div>
			</td>
		
			<td width="300" style="padding: 10px; vertical-align: top; line-height: 1.4em;">
				<div style="color: #2995c0; font-size: 12px; font-weight: bold;">
					<!-- job title -->
					<?php echo $job->getTitle() . ""; ?>
				</div>

				<div style="color: #444444; font-size: 11px; font-weight: bold;">
					<!-- company title -->
					<?php echo $company->getTitle() . ""; ?>
				</div>

				<div style="color: #999999;">
					<!-- location -->
					<?php echo $job -> working_place; ?>
				</div>
			</td>

			<td width="200" style="padding: 10px; vertical-align: top;">
				<div style="text-transform: capitalize; color: #e54549; font-size: 10px; font-weight: bold;">
				<!-- salary -->
				<?php if(($job -> salary_from == 0) && ($job -> salary_to == 0)) :?>
					<?php echo $this -> translate('Negotiable') . "<br />"; ?>
				<?php else :?>
					<?php echo $this -> locale() -> toCurrency($job->salary_from, $job->salary_currency) . ' - ' . $this -> locale() -> toCurrency($job->salary_to, $job->salary_currency) . "<br />"; ?>
				<?php endif;?>
				</div>

				<div style="margin-top: 5px;">
				<!-- view job -->
				<?php echo "<a style='display: inline-block; text-decoration: none; border: 1px solid #2995c0; color: #2995c0; padding: 3px 10px; border-radius: 3px; -webkit-border-radius: 3px; -moz-border-radius: 3px;' href='" . 'http://' . $_SERVER['HTTP_HOST'] . $job -> getHref() . "'>" . $this -> translate('VIEW JOB >>') . "</a>"; ?>
				</div>
			</td>
		</tr>
	<?php endforeach;?>

	<tr>
		<td colspan="3" style="height: 5px; background-image: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Ynjobposting/externals/images/pattern_letter.png'; ?>);"></td>
	</tr>
	<tr>
		<td colspan="3" style="font-size: 10px; color: #fff; text-align: center; height: 55px; vertical-align: middle; background-color: #d1312e; ">
		<?php if(!empty($this -> toSendJobIds)) :?>
			<!-- unsubsribe -->
			<?php $unsubscribeUrl = $this -> url(array('controller' => 'jobs', 'action' => 'unsubscribe', 'email' => $this -> email), 'ynjobposting_extended', true); ?>

			<a style="color: #fff; text-decoration: none;" href='<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $unsubscribeUrl ?>'><?php echo $this -> translate('UNSUBSCRIBE >>') ?></a>
		<?php endif;?>
		</td>
	</tr>
</table>