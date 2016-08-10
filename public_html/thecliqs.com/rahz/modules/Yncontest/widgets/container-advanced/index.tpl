<div class='generic_layout_container'>
	<table style="vertical-align: top;width:100%;overflow: hidden">
		<tr>
			<?php
			$index = 0;
            $total = count($this->elements);
			foreach ($this->elements as $child):
            $width = (isset($this->widths[$index]) && $this->widths[$index])?$this->widths[$index]:'auto'; 
            $index +=1;

			?>
			<td style="vertical-align:top;overflow:hidden" width="<?php echo $width?>"><?php echo $child -> render();?></td>
			<?php if($index<$total): ?>
<td width="<?php echo $this->padding ?>" style="vertical-align:top;overflow:hidden">&nbsp;</td>
<?php endif;?>
			<?php endforeach;?>
		</tr>
	</table>
</div>