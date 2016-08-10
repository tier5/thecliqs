<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->listing->__toString();
				echo $this->translate('&#187; Albums');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main">
	<?php
		echo $this->form->render();
	?>
</div>
